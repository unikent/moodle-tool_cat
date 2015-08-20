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

namespace tool_cat\target;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/lib.php");

/**
 * Category admin tool course target.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends base
{
    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public function get_supported_datatypes() {
        return array(
            'section'
        );
    }

    /**
     * Create a section.
     */
    private function create_section($course, $section, $prepend = false) {
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();

        $sectionnumber = 0;
        foreach ($sections as $section) {
            if ($section->section > $sectionnumber) {
                $sectionnumber = $section->section + 1;
            }
        }

        section::create_section($course, array(
            'section' => $sectionnumber,
            'name' => $section->name,
            'visible' => $section->visible,
            'summary' => $section->summary,
            'summaryformat' => $section->summaryformat,
            'availability' => $section->availability
        ));

        if ($prepend) {
            move_section_to($course, $sectionnumber, 0);
        }
    }

    /**
     * Append a section.
     */
    public function append_to($courses) {
        $section = $this->datatype->get_section();
        foreach ($courses as $course) {
            $this->create_section($course, $section);
        }
    }

    /**
     * Delete a course.
     */
    public function delete($courses) {
        foreach ($courses as $course) {
            delete_course($course, false);
        }
    }

    /**
     * Prepend a section.
     */
    public function prepend_to($courses) {
        $section = $this->datatype->get_section();
        foreach ($courses as $course) {
            $this->create_section($course, $section, true);
        }
    }
}
