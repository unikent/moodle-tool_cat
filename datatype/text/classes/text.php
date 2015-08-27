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
 * @package    catdatatype_text
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catdatatype_text;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool text data type.
 * Optionally rendered by the Mustache library.
 *
 * @package    catdatatype_text
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text extends \tool_cat\datatype
{
    /**
     * Get the section text for a course.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     * @return string                  The current section text.
     */
    public function get_section_text($course, $sectionident) {
        global $DB;

        return $DB->get_field('course_sections', 'summary', array(
            'course' => $course->id,
            'section' => $sectionident
        ));
    }
    /**
     * Get the section text for a course.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     * @param  string   $text          The new section text.
     */
    public function set_section_text($course, $sectionident, $text) {
        global $DB;

        $DB->set_field('course_sections', 'summary', $text, array(
            'course' => $course->id,
            'section' => $sectionident
        ));
    }

    /**
     * Append this text to the given course/section.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     */
    public function append_to_section($course, $sectionident) {
        $data = $this->get_data();

        $text = $this->get_section_text($course, $sectionident);
        $text .= $data->text;

        $this->set_section_text($course, $sectionident, $text);
    }

    /**
     * Prepend this text to the given course/section.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     */
    public function prepend_to_section($course, $sectionident) {
        $data = $this->get_data();

        $text = $data->text;
        $text .= $this->get_section_text($course, $sectionident);

        $this->set_section_text($course, $sectionident, $text);
    }
}
