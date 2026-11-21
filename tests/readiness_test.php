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

namespace tool_healthcheck;

use PHPUnit\Framework\Attributes\CoversClass;
use tool_healthcheck\hook\readiness_checks;
use tool_healthcheck\local\checks;
use tool_healthcheck\local\readiness;

/**
 * Tests of the readiness checks, their isolation and their lack of side effects.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(readiness::class)]
#[CoversClass(readiness_checks::class)]
#[CoversClass(checks\database::class)]
#[CoversClass(checks\dataroot::class)]
#[CoversClass(checks\cache::class)]
#[CoversClass(checks\sessions::class)]
#[CoversClass(checks\core_status::class)]
final class readiness_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        require_once(__DIR__ . '/fixtures/test_check.php');
        require_once(__DIR__ . '/fixtures/hook_callbacks.php');
    }

    /**
     * Uses a hook manager with only the given callbacks.
     *
     * @param array $callbacks Callback definitions (as in db/hooks.php).
     */
    private function use_callbacks(array $callbacks): void {
        $file = make_request_directory() . '/hooks.php';
        file_put_contents($file, '<?php $callbacks = ' . var_export($callbacks, true) . ';');
        \core\di::set(\core\hook\manager::class, \core\hook\manager::phpunit_get_instance(['local_objectstore' => $file]));
    }

    /**
     * Default and configured optional checks.
     */
    public function test_enabled(): void {
        $this->resetAfterTest();
        unset_config('checks', 'tool_healthcheck');
        $this->assertSame(readiness::DEFAULT, readiness::enabled());
        set_config('checks', 'sessions,unknown,dataroot', 'tool_healthcheck');
        $this->assertSame(['dataroot', 'sessions'], readiness::enabled());
        set_config('checks', '', 'tool_healthcheck');
        $this->assertSame([], readiness::enabled());
    }

    /**
     * Built-in checks pass on a healthy site and leave nothing behind.
     */
    public function test_builtin_checks_have_no_side_effects(): void {
        $this->resetAfterTest();
        $this->use_callbacks([]);
        $report = readiness::run(readiness::checks(readiness::DEFAULT), true);
        $this->assertSame('ok', $report['status']);
        $this->assertSame(['database', 'dataroot', 'cache', 'sessions'], array_keys($report['checks']));

        $this->assertFileDoesNotExist(checks\dataroot::probe_file());
        $this->assertFalse(\cache::make('tool_healthcheck', 'probe')->get(checks\cache::probe_key()));
    }

    /**
     * A failing check does not stop the others; the probe is removed even after a failure.
     */
    public function test_failing_check(): void {
        global $CFG;
        $this->resetAfterTest();
        // A file where the probe folder should be: the dataroot check cannot create it.
        $dir = $CFG->dataroot . '/temp/tool_healthcheck';
        remove_dir($dir);
        check_dir_exists(dirname($dir));
        file_put_contents($dir, 'blocker');
        try {
            $report = readiness::run(['database' => new checks\database(), 'dataroot' => new checks\dataroot()], false);
        } finally {
            unlink($dir);
        }
        $this->assertSame('fail', $report['status']);
        $this->assertSame('ok', $report['checks']['database']['status']);
        $this->assertSame('fail', $report['checks']['dataroot']['status']);
        $this->assertArrayNotHasKey('message', $report['checks']['dataroot']);
        $this->assertArrayHasKey('time_ms', $report['checks']['dataroot']);
    }

    /**
     * Contributed checks: valid ones run, invalid ones are rejected, messages only with details.
     */
    public function test_extensions(): void {
        $this->resetAfterTest();
        $this->use_callbacks([
            ['hook' => readiness_checks::class, 'callback' => [fixtures\hook_callbacks::class, 'contribute']],
        ]);
        $checks = readiness::checks(['extensions']);
        $this->assertCount(3, $this->getDebuggingMessages());
        $this->resetDebugging();
        $this->assertSame(['database', 'local_objectstore:bucket', 'local_objectstore:replica'], array_keys($checks));

        $report = readiness::run($checks, true);
        $this->assertSame('fail', $report['status']);
        $this->assertSame('ok', $report['checks']['local_objectstore:bucket']['status']);
        $this->assertSame('Replica unreachable', $report['checks']['local_objectstore:replica']['message']);
    }

    /**
     * A throwing callback makes the "extensions" check fail without breaking the response.
     */
    public function test_extension_failure(): void {
        $this->resetAfterTest();
        $this->use_callbacks([
            ['hook' => readiness_checks::class, 'callback' => [fixtures\hook_callbacks::class, 'fail']],
        ]);
        $report = readiness::run(readiness::checks(['extensions']), false);
        $this->assertSame('fail', $report['status']);
        $this->assertSame('ok', $report['checks']['database']['status']);
        $this->assertSame('fail', $report['checks']['extensions']['status']);
    }

    /**
     * Core status checks: ERROR and CRITICAL fail, the others pass.
     */
    public function test_core_status(): void {
        $this->resetAfterTest();
        $make = function (string $status): checks\core_status {
            $check = $this->createMock(\core\check\check::class);
            $check->method('get_ref')->willReturn('tool_x_' . $status);
            $check->method('get_result')->willReturn(new \core\check\result($status, 'Summary of ' . $status));
            return new checks\core_status($check);
        };
        $checks = [];
        foreach ([\core\check\result::OK, \core\check\result::WARNING, \core\check\result::ERROR] as $status) {
            $check = $make($status);
            $checks[$check->get_name()] = $check;
        }
        $report = readiness::run($checks, true);
        $this->assertSame('ok', $report['checks']['core:tool_x_ok']['status']);
        $this->assertSame('ok', $report['checks']['core:tool_x_warning']['status']);
        $this->assertSame('fail', $report['checks']['core:tool_x_error']['status']);
        $this->assertSame('Summary of error', $report['checks']['core:tool_x_error']['message']);
        $this->assertNotEmpty(checks\core_status::all());
    }
}
