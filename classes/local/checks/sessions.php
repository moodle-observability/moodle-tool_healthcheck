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

namespace tool_healthcheck\local\checks;

use tool_healthcheck\local\check;

/**
 * Session handler: connects to the configured handler and looks up a session id that never exists (read only).
 *
 * No session is created: the endpoint runs with NO_MOODLE_COOKIES.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class sessions implements check {
    /** Session id looked up by the check; never created. */
    public const PROBE_SID = 'tool-healthcheck-probe';

    #[\Override]
    public function get_name(): string {
        return 'sessions';
    }

    #[\Override]
    public function run(): void {
        $class = \core\session\manager::get_handler_class();
        $handler = new $class();
        $handler->init();
        // The answer does not matter: the lookup fails with an exception when the handler is unreachable.
        $handler->session_exists(self::PROBE_SID);
    }
}
