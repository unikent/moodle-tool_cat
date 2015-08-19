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

require_once("$CFG->libdir/coursecatlib.php");

/**
 * Category admin tool core.
 *
 * @package    tool_cat
 * @copyright  2014 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core
{
    const STATUS_SCHEDULED = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_ERROR = 2;

    /**
     * Creates a category for the manager to use.
     */
    private static function create_category() {
        // Create a category.
        $category = new \stdClass();
        $category->parent = 0;
        $category->idnumber = 'catman_removed';
        $category->name = 'Removed';
        $category->description = 'Holding place for removed modules.';
        $category->sortorder = 999;
        $category->visible = false;

        return \coursecat::create($category);
    }

    /**
     * Returns the category the category manager is supposed to use.
     */
    public static function get_category() {
        // Try to use the category we have set if we have it set.
        $catid = get_config("tool_cat", "catid");
        if ($catid > 0) {
            $category = \coursecat::get($catid, \IGNORE_MISSING, true);
            if ($category) {
                return $category;
            }
        }

        // Create it.
        $obj = self::create_category();

        // Store it.
        set_config("catid", $obj->id, "tool_cat");

        return $obj;
    }

    /**
     * Get the period of holding.
     */
    public static function get_holding_period() {
        $period = get_config("tool_cat", "period");
        if ($period === false) {
            $period = 1209600; // 14 day default.
        }

        return $period;
    }

    /**
     * Delay the given course.
     */
    public static function delay($id) {
        global $DB;

        // Grab the course.
        $course = $DB->get_record('tool_cat_recyclebin', array(
            'id' => $id
        ), 'id,expiration_time', MUST_EXIST);

        // Delay the given course.
        $DB->set_field('tool_cat_recyclebin', 'expiration_time', $course->expiration_time + self::get_holding_period(), array(
            'id' => $id
        ));
    }

    /**
     * Returns the expiration_time of a course.
     * -1 if the course is unscheduled.
     */
    public static function get_expiration($courseorid) {
        global $DB;

        $record = $DB->get_record('tool_cat_recyclebin', array(
            'courseid' => is_object($courseorid) ? $courseorid->id : $courseorid,
            'status' => static::STATUS_SCHEDULED
        ));

        if (!$record) {
            return -1;
        }

        return (int)$record->expiration_time;
    }

    /**
     * Is this course scheduled for deletion?
     */
    public static function is_scheduled($courseorid) {
        global $DB;

        return $DB->record_exists('tool_cat_recyclebin', array(
            'courseid' => is_object($courseorid) ? $courseorid->id : $courseorid,
            'status' => static::STATUS_SCHEDULED
        ));
    }
}