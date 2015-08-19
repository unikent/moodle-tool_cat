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
 * Category admin tool recyclebin.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('categoryadmintoolrecyclebin', '', null, '', array(
    'pagelayout' => 'report'
));

$PAGE->set_context(\context_system::instance());
$PAGE->set_url('/admin/tool/cat/recyclebin.php');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_cat'));

// Allow the user to delay a purge.
$action = optional_param('action', false, PARAM_ALPHA);
if ($action == 'delay') {
    $id = required_param('id', PARAM_INT);

    // Delay it.
    \tool_cat\core::delay($id);

    // Let the user know.
    echo $OUTPUT->notification(get_string('delay_success', 'tool_cat'));
}

// Create a table.
$table = new \html_table();
$table->head = array(
    get_string('course', 'tool_cat'),
    get_string('date_deleted', 'tool_cat'),
    get_string('date_scheduled', 'tool_cat'),
    get_string('status', 'tool_cat'),
    get_string('action', 'tool_cat')
);
$table->data = array();

// Get all the entries.
$entries = $DB->get_records_sql("
    SELECT ce.id, ce.courseid, ce.deleted_date, ce.expiration_time, ce.status, COALESCE(c.shortname, 'Deleted') as shortname
        FROM {tool_cat_recyclebin} ce
    LEFT OUTER JOIN {course} c
        ON c.id = ce.courseid
    ORDER BY ce.expiration_time DESC
");

$timeformat = get_string('strftimedatetime');
$delay = get_string('delay', 'tool_cat');

// Add all the entries to the table.
foreach ($entries as $entry) {
    $courselink = new \html_table_cell(\html_writer::link(
        new \moodle_url('/course/view.php', array('id' => $entry->courseid)),
        $entry->shortname,
        array('target' => '_blank')
    ));

    $actionlink = new \html_table_cell(\html_writer::link(
        new \moodle_url('/admin/tool/cat/recyclebin.php', array(
            'action' => 'delay',
            'id' => $entry->id
        )),
        $delay
    ));

    $table->data[] = new \html_table_row(array(
        $courselink,
        strftime($timeformat, $entry->deleted_date),
        strftime($timeformat, $entry->expiration_time),
        get_string("status_{$entry->status}", 'tool_cat'),
        $actionlink
    ));
}

echo $OUTPUT->box(\html_writer::table($table));

echo $OUTPUT->footer();