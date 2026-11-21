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

use core\check\result;
use tool_healthcheck\local\check;

/**
 * Wraps a "status" check of the core Check API (\core\check\manager::get_checks('status')).
 *
 * ERROR and CRITICAL fail the readiness; the other results pass.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class core_status implements check {
    /** @var \core\check\check Wrapped check. */
    private \core\check\check $check;

    /**
     * Constructor.
     *
     * @param \core\check\check $check Core check.
     */
    public function __construct(\core\check\check $check) {
        $this->check = $check;
    }

    /**
     * One wrapper per status check of the site.
     *
     * @return self[]
     */
    public static function all(): array {
        return array_map(fn(\core\check\check $check): self => new self($check), \core\check\manager::get_checks('status'));
    }

    #[\Override]
    public function get_name(): string {
        return 'core:' . preg_replace('/[^a-z0-9_:-]/', '_', strtolower($this->check->get_ref()));
    }

    #[\Override]
    public function run(): void {
        $result = $this->check->get_result();
        if (in_array($result->get_status(), [result::ERROR, result::CRITICAL], true)) {
            throw new \moodle_exception('generalexceptionmessage', 'error', '', strip_tags((string) $result->get_summary()));
        }
    }
}
