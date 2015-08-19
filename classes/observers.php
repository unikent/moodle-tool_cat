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

namespace tool_cat;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool observers.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observers
{
    /**
     * Triggered when 'course_updated' event is triggered.
     * Adds a course expiration date if the course has moved category.
     *
     * @param \core\event\course_updated $event
     * @return unknown
     */
    public static function course_updated(\core\event\course_updated $event) {
        global $CFG, $DB;

        $enabled = get_config("tool_cat", "enablerecyclebin");
        if (!$enabled) {
            return true;
        }

        // Grab the course.
        $course = $DB->get_record('course', array(
            "id" => $event->objectid
        ));
        $context = \context_course::instance($course->id);

        // The ID of the deleted category is stored in config.
        $category = recyclebin::get_category();

        // Does the course exist in the expiration table already?
        if ($DB->record_exists("tool_cat_recyclebin", array("courseid" => $event->objectid))) {
            if ($course->category !== $category->id) {
                // Delete the record from the expiration table.
                $DB->delete_records("tool_cat_recyclebin", array(
                    "courseid" => $event->objectid
                ));

                // Remove notification.
                $notification = \tool_cat\notification\recyclebin::get($course->id, $context);
                if ($notification) {
                    $notification->delete();
                }

                // Schedule an event.
                $event = \tool_cat\event\recyclebin_unscheduled::create(array(
                    'objectid' => $course->id,
                    'courseid' => $course->id,
                    'context' => $context
                ));
                $event->trigger();
            }

            return true;
        }

        // Is this now in the deleted category?
        if ($course->category === $category->id) {
            require_once($CFG->libdir . '/enrollib.php');
            require_once($CFG->dirroot . '/course/lib.php');

            // Delete enrolments.
            enrol_course_delete($course);

            // Insert a record into the DB.
            $expiration = time() + recyclebin::get_holding_period();
            $DB->insert_record("tool_cat_recyclebin", array(
                "courseid" => $course->id,
                "deleted_date" => time(),
                "expiration_time" => $expiration
            ));

            // Hide it.
            $course->visible = false;
            update_course($course);

            // Create the notification.
            \tool_cat\notification\recyclebin::create(array(
                'objectid' => $course->id,
                'context' => $context,
                'other' => array(
                    'expirationtime' => $expiration
                )
            ));

            // Schedule an event.
            $event = \tool_cat\event\recyclebin_scheduled::create(array(
                'objectid' => $course->id,
                'courseid' => $course->id,
                'context' => $context,
                'other' => array(
                    'expirationtime' => $expiration
                )
            ));
            $event->add_record_snapshot('course', $course);
            $event->trigger();
        }

        return true;
    }
}