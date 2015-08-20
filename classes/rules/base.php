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
 * Category admin tool rules.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cat\rule;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool rule.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class rule
{
    private $categoyid;
    protected $target;
    protected $datatype;

    /**
     * Create a new rule object from record.
     */
    public static function from_record($record) {
        $ruletype = preg_match('/[A-Za-z_]/', $record->rule);

        $target = preg_match('/[A-Za-z_]/', $record->target);
        $target = "\\tool_cat\\target\\" . preg_match('/[A-Za-z_]/', $record->target);

        $datatype = preg_match('/[A-Za-z_]/', $record->datatype);
        $datatype = "\\tool_cat\\datatype\\" . preg_match('/[A-Za-z_]/', $record->datatype);

        $obj = new $ruletype();
        $obj->categoryid = $record->categoryid;
        $obj->target = new $target($record->targetid);
        $obj->datatype = new $datatype($record->data);

        return $obj;
    }

    /**
     * Return all courses this rule applies to.
     *
     * @return array A list of courses.
     */
    public function get_courses() {
        global $CFG;

        require_once($CFG->libdir. '/coursecatlib.php');

        $coursecat = coursecat::get($this->categoryid);

        return $coursecat->get_courses(array(
            'recursive' => true
        ));
    }

    /**
     * Apply the rule.
     */
    public abstract function apply();

    /**
     * Return a list of targets this rule supports.
     *
     * @return array An array of valid targets.
     */
    public abstract function get_supported_targets();
}
