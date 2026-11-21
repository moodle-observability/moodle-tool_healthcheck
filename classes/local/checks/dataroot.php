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
 * moodledata is writable: writes, reads back and removes a probe file with a fixed name per node.
 *
 * The fixed name means a probe left behind by a killed process is overwritten on the next run, never accumulated.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class dataroot implements check {
    #[\Override]
    public function get_name(): string {
        return 'dataroot';
    }

    /**
     * Path of the probe file of this node.
     *
     * @return string
     */
    public static function probe_file(): string {
        global $CFG;
        $host = preg_replace('/[^a-zA-Z0-9_.-]/', '_', gethostname() ?: 'node');
        return $CFG->dataroot . '/temp/tool_healthcheck/probe-' . $host;
    }

    #[\Override]
    public function run(): void {
        $file = self::probe_file();
        $dir = dirname($file);
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \moodle_exception('generalexceptionmessage', 'error', '', 'Cannot create the probe folder');
        }
        $value = bin2hex(random_bytes(8));
        try {
            if (@file_put_contents($file, $value) !== strlen($value)) {
                throw new \moodle_exception('generalexceptionmessage', 'error', '', 'moodledata is not writable');
            }
            clearstatcache(true, $file);
            if (@file_get_contents($file) !== $value) {
                throw new \moodle_exception('generalexceptionmessage', 'error', '', 'moodledata did not return the written data');
            }
        } finally {
            @unlink($file);
        }
    }
}
