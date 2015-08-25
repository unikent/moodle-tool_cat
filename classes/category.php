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
 * Category admin tool rules entry class.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category
{
    private $id;
    private $courses = null;

    /**
     * Constructor.
     *
     * @param int $id Category ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Return all courses this rule applies to.
     *
     * @return array A list of courses.
     */
    public function get_courses() {
        global $CFG, $DB;

        if (isset($this->courses)) {
            return $this->courses;
        }

        require_once($CFG->libdir. '/coursecatlib.php');

        $coursecat = \coursecat::get($this->id);
        $courselist = $coursecat->get_courses(array(
            'recursive' => true,
            'idonly' => true
        ));

        // Generate the SQL.
        list($sql, $params) = $DB->get_in_or_equal($courselist);
        $ctxlevel = \CONTEXT_COURSE;
        $preload = \context_helper::get_preload_record_columns_sql('ctx');

        $sql = <<<SQL
            SELECT c.*, $preload
            FROM {course} c
            INNER JOIN {context} ctx
                ON ctx.instanceid = c.id AND ctx.contextlevel = $ctxlevel
            WHERE c.id $sql
SQL;

        // Get the courses and preload contexts.
        $this->courses = $DB->get_records_sql($sql, $params);
        foreach ($this->courses as $course) {
            \context_helper::preload_from_record($course);
        }

        return $this->courses;
    }

    /**
     * Returns all known category rules to this category.
     * This includes parent categories.
     */
    public function get_rules() {
        global $DB;

        $coursecat = \coursecat::get($this->id);
        $parents = $coursecat->get_parents();
        $categories = array_merge($parents, array($this->id));

        list($sql, $params) = $DB->get_in_or_equal($categories);
        $rules = $DB->get_records_select('tool_cat_rules', 'categoryid ' . $sql, $params);

        // Sort.
        // Parent categories first, then by seq.
        $ret = array();
        foreach ($categories as $category) {
            $buffer = array();
            foreach ($rules as $rule) {
                if ($rule->categoryid != $category) {
                    continue;
                }

                $buffer[$rule->seq] = $rule;
            }

            // Add the buffer in.
            ksort($buffer);
            foreach ($buffer as $rule) {
                $ret[] = \tool_cat\rule\base::from_record($rule);
            }
        }

        return $ret;
    }

    /**
     * Apply all known category rules to this category.
     */
    public function apply_rules() {
        $courses = $this->get_courses();

        $rules = $this->get_rules();
        foreach ($rules as $rule) {
            $rule->apply($courses);
        }
    }

    /**
     * Apply a rule to this category.
     *
     * @param rule\base $rule The rule to apply.
     */
    public function apply($rule) {
        $courses = $this->get_courses();
        $rule->apply($courses);
    }
}