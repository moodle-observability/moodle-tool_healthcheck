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

namespace tool_healthcheck\hook;

use tool_healthcheck\local\check;

/**
 * Lets other plugins contribute readiness checks (for example object storage through tool_objectfs).
 *
 * Contributed checks must have no side effects: read only when possible (an object store check uses HeadBucket or
 * ListObjectsV2 with max-keys=1, never Put/Delete/Copy); when something must be written, use a fixed name per node
 * and remove it in a finally block. Keep them fast and never put sensitive data in exception messages.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to contribute checks to the readiness endpoint of tool_healthcheck.')]
#[\core\attribute\tags('healthcheck')]
final class readiness_checks {
    /** @var array<string, check> Checks keyed by "<component>:<name>". */
    private array $checks = [];

    /** @var string[] Rejected contributions. */
    private array $errors = [];

    /**
     * Adds a check. Its name in the response is "<component>:<check name>".
     *
     * @param string $component Frankenstyle name of the contributing plugin.
     * @param check $check Check.
     * @return void
     */
    public function add_check(string $component, check $check): void {
        $clean = clean_param($component, PARAM_COMPONENT);
        $name = $component . ':' . $check->get_name();
        if ($clean === '' || $clean !== $component || !str_contains($component, '_')) {
            $this->errors[] = "Invalid component: {$component}";
        } else if (!preg_match('/^[a-z0-9_:-]{1,100}$/', $name)) {
            $this->errors[] = "Invalid check name: {$name}";
        } else if (isset($this->checks[$name])) {
            $this->errors[] = "Duplicated check: {$name}";
        } else {
            $this->checks[$name] = $check;
        }
    }

    /**
     * Contributed checks keyed by their name in the response.
     *
     * @return array<string, check>
     */
    public function get_checks(): array {
        return $this->checks;
    }

    /**
     * Messages of the rejected contributions.
     *
     * @return string[]
     */
    public function get_errors(): array {
        return $this->errors;
    }
}
