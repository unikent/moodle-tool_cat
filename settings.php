<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Category admin tool settings.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('tools', new admin_externalpage(
        'categoryadmintool',
        get_string('pluginname', 'tool_cat'),
        new \moodle_url("/admin/tool/cat/index.php")
    ));

    $ADMIN->add('tools', new admin_externalpage(
        'categoryadmintoolrecyclebin',
        get_string('recyclebin', 'tool_cat'),
        new \moodle_url("/admin/tool/cat/recyclebin.php")
    ));

    $settings = new admin_settingpage('tool_cat', get_string('pluginname', 'tool_cat'));
    $ADMIN->add('tools', $settings);

    $settings->add(new admin_setting_configcheckbox(
        'tool_cat/enablerecyclebin',
        get_string('enablerecyclebin', 'tool_cat'),
        '',
        0
    ));

    $settings->add(new admin_setting_configtext(
        'tool_cat/recyclebinid',
        get_string('recyclebinid', 'tool_cat'),
        '',
        0,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'tool_cat/recyclebinexp',
        get_string('recyclebinexp', 'tool_cat'),
        '',
        1209600,
        PARAM_INT
    ));
}
