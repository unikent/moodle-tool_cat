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

namespace tool_cat\task;

/**
 * Purges due courses.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recyclebin extends \core\task\scheduled_task
{
    public function get_name() {
        return get_string('recyclebintaskname', 'tool_cat');
    }

    public function execute() {
        global $DB;

        // Don't run if we are disabled.
        if (!get_config("tool_cat", "enable")) {
            return;
        }

        // Get a list of courses that are due to expire.
        $sql = "SELECT * FROM {tool_cat_recyclebin} WHERE expiration_time < :time AND status = :status";
        $expirations = $DB->get_records_sql($sql, array(
            'time' => time(),
            'status' => \tool_cat\core::STATUS_SCHEDULED
        ));

        // Grab the removed category.
        $category = \tool_cat\core::get_category();

        // Foreach course in the category.
        foreach ($expirations as $expiration) {
            echo "Deleting course {$expiration->courseid}....\n";

            // Set it to status 2 (error) so we don't keep re-trying this if it fails badly.
            $expiration->status = \tool_cat\core::STATUS_ERROR;
            $DB->update_record('tool_cat_recyclebin', $expiration);

            // Grab the course.
            $course = $DB->get_record('course', array(
                'id' => $expiration->courseid,
                'category' => $category->id
            ));
            $coursectx = \context_course::instance($course->id);

            // Did we succeed?
            if ($course === false) {
                continue;
            }

            try {
                // Attempt to delete the course.
                delete_course($course);

                $expiration->status = \tool_cat\core::STATUS_COMPLETED;
            } catch (\Exception $e) {
                $expiration->status = \tool_cat\core::STATUS_ERROR;
                debugging($e->getMessage());
            }

            // Does the course exist?
            // If it does, it didn't work.
            if ($DB->record_exists('course', array('id' => $expiration->courseid))) {
                $expiration->status = \tool_cat\core::STATUS_ERROR;
            }

            // Raise an event.
            if ($expiration->status = \tool_cat\core::STATUS_COMPLETED) {
                $event = \tool_cat\event\recyclebin_purged::create(array(
                    'objectid' => $expiration->courseid,
                    'context' => $coursectx,
                    'other' => array(
                        'shortname' => $course->shortname
                    )
                ));
                $event->trigger();
            }

            $DB->update_record('tool_cat_recyclebin', $expiration);
        }
    }
}
