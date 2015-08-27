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
 * Category admin tool datatypes.
 *
 * @package    catdatatype_news
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catdatatype_news;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/aspirelists/lib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Category admin tool news data type.
 *
 * @package    catdatatype_news
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class news extends \tool_cat\datatype
{
    /**
     * Append a news activity to the given course.
     *
     * @param  stdClass $course        The course to apply to.
     */
    public function add_to_course($course) {
        forum_get_course_forum($course->id, 'news');
    }
}
