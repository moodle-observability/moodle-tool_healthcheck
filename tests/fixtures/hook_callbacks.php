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

namespace tool_healthcheck\fixtures;

use tool_healthcheck\hook\readiness_checks;

/**
 * Callbacks of the readiness_checks hook used by the tests.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hook_callbacks {
    /**
     * Valid and invalid contributions.
     *
     * @param readiness_checks $hook Hook.
     */
    public static function contribute(readiness_checks $hook): void {
        $hook->add_check('local_objectstore', new test_check('bucket'));
        $hook->add_check('local_objectstore', new test_check('replica', 'Replica unreachable'));
        $hook->add_check('local_objectstore', new test_check('bucket'));
        $hook->add_check('Bad Component', new test_check('x'));
        $hook->add_check('local_objectstore', new test_check('Invalid Name'));
    }

    /**
     * A callback that throws.
     *
     * @param readiness_checks $hook Hook.
     */
    public static function fail(readiness_checks $hook): void {
        throw new \moodle_exception('error');
    }
}
