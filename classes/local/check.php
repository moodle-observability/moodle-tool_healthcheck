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
 * A readiness check.
 *
 * Rules: no side effects (read only when possible; when something must be written, use a fixed name per node and
 * remove it in a finally block), a short timeout and no sensitive data in the exception message.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface check {
    /**
     * Name of the check in the JSON response (lowercase letters, digits, "_", "-" and ":").
     *
     * @return string
     */
    public function get_name(): string;

    /**
     * Runs the check. Returns normally when healthy; throws when not.
     *
     * @return void
     * @throws \Throwable When the check fails; the message is shown only when details are enabled.
     */
    public function run(): void;
}
