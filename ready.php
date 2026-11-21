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

/**
 * Readiness endpoint: whether this node can receive traffic (HTTP 200) or not (HTTP 503).
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// The config.php inclusion is inside a try block, to answer 503 when the Moodle setup fails.
// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState
// phpcs:disable moodle.Files.RequireLogin.Missing -- Authenticated by token (tool_healthcheck\local\auth).

define('NO_MOODLE_COOKIES', true);
define('NO_DEBUG_DISPLAY', true);

$start = microtime(true);
try {
    require(__DIR__ . '/../../../config.php');
} catch (\Throwable $e) {
    // The Moodle setup failed (usually the database is down): the node is not ready.
    require_once(__DIR__ . '/classes/local/auth.php');
    $status = \tool_healthcheck\local\auth::check(
        \tool_healthcheck\local\auth::configured_token($CFG),
        \tool_healthcheck\local\auth::bearer_token($_SERVER)
    );
    if ($status !== \tool_healthcheck\local\auth::OK) {
        \tool_healthcheck\local\auth::send_error($status);
        die();
    }
    http_response_code(503);
    \tool_healthcheck\local\auth::send_json([
        'status' => 'fail',
        'time_ms' => round((microtime(true) - $start) * 1000, 2),
        'checks' => ['database' => ['status' => 'fail', 'time_ms' => round((microtime(true) - $start) * 1000, 2)]],
    ]);
    die();
}

$status = \tool_healthcheck\local\auth::check(
    \tool_healthcheck\local\auth::configured_token($CFG),
    \tool_healthcheck\local\auth::bearer_token($_SERVER)
);
if ($status !== \tool_healthcheck\local\auth::OK) {
    \tool_healthcheck\local\auth::send_error($status);
    die();
}

$checks = \tool_healthcheck\local\readiness::checks(\tool_healthcheck\local\readiness::enabled());
$report = \tool_healthcheck\local\readiness::run($checks, (bool) get_config('tool_healthcheck', 'details'));
http_response_code($report['status'] === 'ok' ? 200 : 503);
\tool_healthcheck\local\auth::send_json([
    'status' => $report['status'],
    'time_ms' => round((microtime(true) - $start) * 1000, 2),
    'checks' => $report['checks'],
]);
