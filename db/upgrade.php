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

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade tasks.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_tool_cat_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2015081901) {
        // Define table tool_cat_rules to be created.
        $table = new xmldb_table('tool_cat_rules');

        // Adding fields to table tool_cat_rules.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('seq', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rule', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('target', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('targetid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table tool_cat_rules.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('u_catid_order', XMLDB_KEY_UNIQUE, array('categoryid', 'seq'));

        // Conditionally launch create table for tool_cat_rules.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cat savepoint reached.
        upgrade_plugin_savepoint(true, 2015081901, 'tool', 'cat');
    }

    if ($oldversion < 2015082401) {
        // Define table tool_cat_applications to be created.
        $table = new xmldb_table('tool_cat_applications');

        // Adding fields to table tool_cat_applications.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruleid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_cat_applications.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('u_cid_rid', XMLDB_KEY_UNIQUE, array('courseid', 'ruleid'));

        // Conditionally launch create table for tool_cat_applications.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cat savepoint reached.
        upgrade_plugin_savepoint(true, 2015082401, 'tool', 'cat');
    }

    if ($oldversion < 2015082600) {
        // Define table tool_cat_recyclebin to be created.
        $table = new xmldb_table('tool_cat_recyclebin');

        // Conditionally launch create table for tool_cat_recyclebin.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Cat savepoint reached.
        upgrade_plugin_savepoint(true, 2015082600, 'tool', 'cat');
    }

    if ($oldversion < 2015110300) {
        // Define field type to be added to tool_cat_rules.
        $table = new xmldb_table('tool_cat_rules');
        $field = new xmldb_field('config', XMLDB_TYPE_TEXT, null, null, null, null, null, 'data');

        // Conditionally launch add field type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Kent savepoint reached.
        upgrade_plugin_savepoint(true, 2015110300, 'tool', 'cat');
    }

    return true;
}
