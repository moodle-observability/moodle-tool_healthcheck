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
 * English strings.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['cachedef_probe'] = 'Probe key written and removed by the readiness check';
$string['check_cache'] = 'Application cache (MUC)';
$string['check_corestatus'] = 'Core status checks (Check API)';
$string['check_database'] = 'Database';
$string['check_dataroot'] = 'moodledata is writable';
$string['check_extensions'] = 'Checks contributed by other plugins';
$string['check_sessions'] = 'Session handler';
$string['checks'] = 'Readiness checks';
$string['checks_desc'] = 'Checks run by the readiness endpoint. The database is always checked.';
$string['details'] = 'Show details';
$string['details_desc'] = 'Include the error message of failing checks in the JSON response. Messages may reveal host names; keep it off unless the endpoint is only reachable from inside your network.';
$string['endpoints'] = 'Endpoints';
$string['endpoints_desc'] = 'Liveness: <code>{$a->live}</code><br>Readiness: <code>{$a->ready}</code><br>Both require the header <code>Authorization: Bearer &lt;token&gt;</code>.';
$string['pluginname'] = 'Health check';
$string['privacy:metadata'] = 'The health check plugin does not store personal data.';
$string['token'] = 'Token';
$string['token_configured'] = 'The token is configured in config.php.';
$string['token_desc'] = 'The token is read from <code>$CFG->tool_healthcheck_token</code> in config.php, so the liveness endpoint works without the database. It is never shown or stored here. Example: <code>$CFG->tool_healthcheck_token = getenv(\'MOODLE_HEALTHCHECK_TOKEN\') ?: \'\';</code>';
$string['token_missing'] = 'The token is not configured: both endpoints answer 503.';
