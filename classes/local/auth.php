<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace tool_healthcheck\local;

/**
 * Token authentication of the endpoints.
 *
 * Self-contained (no Moodle library calls) because the liveness endpoint loads it without the database: the token
 * comes from $CFG->tool_healthcheck_token in config.php.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class auth {
    /** Authorized. */
    public const OK = 200;

    /** Missing or invalid token. */
    public const UNAUTHORIZED = 401;

    /** Token not configured. */
    public const NOT_CONFIGURED = 503;

    /**
     * Configured token.
     *
     * @param object $cfg The $CFG object.
     * @return string
     */
    public static function configured_token(object $cfg): string {
        return trim((string) ($cfg->tool_healthcheck_token ?? ''));
    }

    /**
     * Checks the token of a request.
     *
     * @param string $configured Configured token ('' when not configured).
     * @param string|null $presented Token presented by the client.
     * @return int One of the status constants.
     */
    public static function check(string $configured, ?string $presented): int {
        if (trim($configured) === '') {
            return self::NOT_CONFIGURED;
        }
        if ($presented === null || $presented === '' || !hash_equals(trim($configured), $presented)) {
            return self::UNAUTHORIZED;
        }
        return self::OK;
    }

    /**
     * Bearer token of the request, if any. The query string is never accepted.
     *
     * @param array $server The $_SERVER array.
     * @return string|null
     */
    public static function bearer_token(array $server): ?string {
        $header = $server['HTTP_AUTHORIZATION'] ?? $server['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
        if ($header === null && function_exists('getallheaders')) {
            foreach ((array) getallheaders() as $name => $value) {
                if (strtolower((string) $name) === 'authorization') {
                    $header = (string) $value;
                    break;
                }
            }
        }
        if ($header === null || !preg_match('/^\s*Bearer\s+(\S+)\s*$/i', (string) $header, $matches)) {
            return null;
        }
        return $matches[1];
    }

    /**
     * Sends the JSON error response of a status other than OK.
     *
     * @param int $status Status constant.
     * @return void
     */
    public static function send_error(int $status): void {
        $messages = [
            self::UNAUTHORIZED => 'unauthorized',
            self::NOT_CONFIGURED => 'not configured',
        ];
        http_response_code($status);
        if ($status === self::UNAUTHORIZED) {
            header('WWW-Authenticate: Bearer realm="tool_healthcheck"');
        }
        self::send_json(['status' => 'error', 'error' => $messages[$status] ?? 'unauthorized']);
    }

    /**
     * Sends a JSON body that is never cached.
     *
     * @param array $data Data.
     * @return void
     */
    public static function send_json(array $data): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_UNESCAPED_SLASHES) . "\n";
    }
}
