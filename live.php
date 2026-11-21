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
 * Liveness endpoint: PHP answers and config.php loads. Never opens a database connection.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Stop lib/setup.php right after config.php: no libraries, no database, no session.
define('ABORT_AFTER_CONFIG', true);
define('NO_DEBUG_DISPLAY', true);

// phpcs:ignore moodle.Files.RequireLogin.Missing -- Authenticated by token (tool_healthcheck\local\auth).
require(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/local/auth.php');

$start = microtime(true);
$token = \tool_healthcheck\local\auth::configured_token($CFG);
$status = \tool_healthcheck\local\auth::check($token, \tool_healthcheck\local\auth::bearer_token($_SERVER));
if ($status !== \tool_healthcheck\local\auth::OK) {
    \tool_healthcheck\local\auth::send_error($status);
    die();
}
\tool_healthcheck\local\auth::send_json(['status' => 'ok', 'time_ms' => round((microtime(true) - $start) * 1000, 2)]);
