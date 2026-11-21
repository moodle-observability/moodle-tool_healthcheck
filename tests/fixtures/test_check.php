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

use tool_healthcheck\local\check;

/**
 * A configurable check used by the tests.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class test_check implements check {
    /** @var string Name. */
    private string $name;

    /** @var string|null Failure message, null when healthy. */
    private ?string $failure;

    /**
     * Constructor.
     *
     * @param string $name Name.
     * @param string|null $failure Failure message, null when healthy.
     */
    public function __construct(string $name, ?string $failure = null) {
        $this->name = $name;
        $this->failure = $failure;
    }

    #[\Override]
    public function get_name(): string {
        return $this->name;
    }

    #[\Override]
    public function run(): void {
        if ($this->failure !== null) {
            throw new \moodle_exception('generalexceptionmessage', 'error', '', $this->failure);
        }
    }
}
