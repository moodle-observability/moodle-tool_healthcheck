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

use tool_healthcheck\hook\readiness_checks;

/**
 * Builds and runs the readiness checks.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class readiness {
    /** Optional checks (the database is always checked). */
    public const OPTIONAL = ['dataroot', 'cache', 'sessions', 'extensions', 'corestatus'];

    /** Optional checks enabled by default. */
    public const DEFAULT = ['dataroot', 'cache', 'sessions', 'extensions'];

    /**
     * Enabled optional checks.
     *
     * @return string[]
     */
    public static function enabled(): array {
        $config = get_config('tool_healthcheck', 'checks');
        if ($config === false) {
            return self::DEFAULT;
        }
        return array_values(array_intersect(self::OPTIONAL, explode(',', (string) $config)));
    }

    /**
     * Checks to run, keyed by their name in the response.
     *
     * @param string[] $enabled Enabled optional checks.
     * @return array<string, check|\Throwable> A Throwable marks a source of checks that failed (the hook).
     */
    public static function checks(array $enabled): array {
        $checks = ['database' => new checks\database()];
        $builtin = ['dataroot' => checks\dataroot::class, 'cache' => checks\cache::class, 'sessions' => checks\sessions::class];
        foreach ($builtin as $name => $class) {
            if (in_array($name, $enabled, true)) {
                $checks[$name] = new $class();
            }
        }
        if (in_array('corestatus', $enabled, true)) {
            foreach (checks\core_status::all() as $check) {
                $checks[$check->get_name()] = $check;
            }
        }
        if (in_array('extensions', $enabled, true)) {
            $hook = new readiness_checks();
            // Each callback runs on its own: one failure only marks the "extensions" check as failed.
            foreach (\core\di::get(\core\hook\manager::class)->getListenersForEvent($hook) as $callback) {
                try {
                    $callback($hook);
                } catch (\Throwable $e) {
                    $checks['extensions'] = $e;
                }
            }
            foreach ($hook->get_errors() as $error) {
                debugging('tool_healthcheck: check rejected: ' . $error, DEBUG_DEVELOPER);
            }
            $checks += $hook->get_checks();
        }
        return $checks;
    }

    /**
     * Runs the checks. A failing check never stops the others.
     *
     * @param array $checks Checks keyed by name.
     * @param bool $details Include the error messages.
     * @return array{status: string, checks: array<string, array>}
     */
    public static function run(array $checks, bool $details): array {
        $results = [];
        $healthy = true;
        foreach ($checks as $name => $check) {
            $start = microtime(true);
            $error = null;
            try {
                if ($check instanceof \Throwable) {
                    throw $check;
                }
                $check->run();
            } catch (\Throwable $e) {
                $error = $e;
            }
            $result = ['status' => $error ? 'fail' : 'ok', 'time_ms' => round((microtime(true) - $start) * 1000, 2)];
            if ($error && $details) {
                $result['message'] = self::message($error);
            }
            $results[$name] = $result;
            $healthy = $healthy && !$error;
        }
        return ['status' => $healthy ? 'ok' : 'fail', 'checks' => $results];
    }

    /**
     * Message of a failure, without the debug information of moodle_exception.
     *
     * @param \Throwable $error Failure.
     * @return string
     */
    private static function message(\Throwable $error): string {
        $message = ($error instanceof \moodle_exception && $error->a !== null && is_string($error->a))
            ? $error->a
            : $error->getMessage();
        return \core_text::substr(trim(strip_tags($message)), 0, 300);
    }
}
