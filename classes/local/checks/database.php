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
 * Database: a read only query.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class database implements check {
    #[\Override]
    public function get_name(): string {
        return 'database';
    }

    #[\Override]
    public function run(): void {
        global $DB;
        if ((int) $DB->get_field_sql('SELECT 1') !== 1) {
            throw new \moodle_exception('generalexceptionmessage', 'error', '', 'Unexpected answer from the database');
        }
    }
}
