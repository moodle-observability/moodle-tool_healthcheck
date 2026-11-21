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
 * Application cache (MUC): sets, reads and deletes a probe key with a fixed name per node and a short TTL.
 *
 * Uses the store mapped to application caches (Redis, memcached, file...), so it detects an unreachable store.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cache implements check {
    #[\Override]
    public function get_name(): string {
        return 'cache';
    }

    /**
     * Key of the probe of this node.
     *
     * @return string
     */
    public static function probe_key(): string {
        return 'probe_' . substr(sha1(gethostname() ?: 'node'), 0, 16);
    }

    #[\Override]
    public function run(): void {
        $cache = \cache::make('tool_healthcheck', 'probe');
        $key = self::probe_key();
        $value = bin2hex(random_bytes(8));
        try {
            if (!$cache->set($key, $value)) {
                throw new \moodle_exception('generalexceptionmessage', 'error', '', 'The application cache refused the write');
            }
            if ($cache->get($key) !== $value) {
                throw new \moodle_exception('generalexceptionmessage', 'error', '', 'The application cache lost the value');
            }
        } finally {
            $cache->delete($key);
        }
    }
}
