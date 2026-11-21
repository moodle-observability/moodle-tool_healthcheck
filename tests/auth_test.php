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
use tool_healthcheck\local\auth;

/**
 * Tests of the token authentication.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(auth::class)]
final class auth_test extends \advanced_testcase {
    /**
     * Token decisions.
     */
    public function test_check(): void {
        $this->assertSame(auth::NOT_CONFIGURED, auth::check('', 'abc'));
        $this->assertSame(auth::NOT_CONFIGURED, auth::check('   ', 'abc'));
        $this->assertSame(auth::UNAUTHORIZED, auth::check('secret', null));
        $this->assertSame(auth::UNAUTHORIZED, auth::check('secret', ''));
        $this->assertSame(auth::UNAUTHORIZED, auth::check('secret', 'SECRET'));
        $this->assertSame(auth::OK, auth::check('secret', 'secret'));
        $this->assertSame(auth::OK, auth::check(" secret\n", 'secret'));
    }

    /**
     * The token comes only from $CFG->tool_healthcheck_token.
     */
    public function test_configured_token(): void {
        $this->assertSame('', auth::configured_token((object) []));
        $this->assertSame('abc', auth::configured_token((object) ['tool_healthcheck_token' => ' abc ']));
    }

    /**
     * Bearer header parsing; the query string is never used.
     */
    public function test_bearer_token(): void {
        $this->assertSame('abc', auth::bearer_token(['HTTP_AUTHORIZATION' => 'Bearer abc']));
        $this->assertSame('abc', auth::bearer_token(['REDIRECT_HTTP_AUTHORIZATION' => 'bearer abc']));
        $this->assertNull(auth::bearer_token(['HTTP_AUTHORIZATION' => 'Basic abc']));
        $this->assertNull(auth::bearer_token(['QUERY_STRING' => 'token=abc']));
    }
}
