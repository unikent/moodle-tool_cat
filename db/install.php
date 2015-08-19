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
 * Category admin tool.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_tool_cat_install() {
    global $CFG, $DB;

    // Is local_catman installed?
    $dbman = $DB->get_manager();
    $table = new xmldb_table('catman_expirations');
    if (!$dbman->table_exists($table)) {
        return true;
    }

    // Port over from local_catman.
    $expirations = $DB->get_records('catman_expirations');
    $DB->insert_records('tool_cat_recyclebin', $expirations);

    // Delete local_catman table.
    $dbman->drop_table($table);

    // Port over config.
    $config = get_config('local_catman');
    foreach ($config as $k => $v) {
        set_config($k, $v, 'tool_cat');
    }

    unset_all_config_for_plugin('local_catman');

    return true;
}
