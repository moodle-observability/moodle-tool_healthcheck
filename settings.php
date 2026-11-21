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

/**
 * Admin settings.
 *
 * @package    tool_healthcheck
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_healthcheck', get_string('pluginname', 'tool_healthcheck'));
    $ADMIN->add('tools', $settings);

    if ($ADMIN->fulltree) {
        $urls = (object) [
            'live' => (new moodle_url('/admin/tool/healthcheck/live.php'))->out(false),
            'ready' => (new moodle_url('/admin/tool/healthcheck/ready.php'))->out(false),
        ];
        $settings->add(new admin_setting_heading(
            'tool_healthcheck/endpoints',
            get_string('endpoints', 'tool_healthcheck'),
            get_string('endpoints_desc', 'tool_healthcheck', $urls)
        ));

        // The token lives in config.php; only whether it is configured is shown.
        $configured = \tool_healthcheck\local\auth::configured_token($CFG) !== '';
        $state = html_writer::tag(
            'p',
            get_string($configured ? 'token_configured' : 'token_missing', 'tool_healthcheck'),
            ['class' => $configured ? 'text-success' : 'text-danger']
        );
        $settings->add(new admin_setting_description(
            'tool_healthcheck/token',
            get_string('token', 'tool_healthcheck'),
            $state . html_writer::tag('p', get_string('token_desc', 'tool_healthcheck'))
        ));

        $choices = [];
        foreach (\tool_healthcheck\local\readiness::OPTIONAL as $check) {
            $choices[$check] = get_string('check_' . $check, 'tool_healthcheck');
        }
        $settings->add(new admin_setting_configmulticheckbox(
            'tool_healthcheck/checks',
            get_string('checks', 'tool_healthcheck'),
            get_string('checks_desc', 'tool_healthcheck'),
            array_fill_keys(\tool_healthcheck\local\readiness::DEFAULT, 1),
            $choices
        ));
        $settings->add(new admin_setting_configcheckbox(
            'tool_healthcheck/details',
            get_string('details', 'tool_healthcheck'),
            get_string('details_desc', 'tool_healthcheck'),
            0
        ));
    }
}
