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
 * Category admin tool section target.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section extends base
{
    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public function get_supported_datatypes() {
        return array(
            'activity', 'text'
        );
    }

    /**
     * Create a section.
     */
    public static function create_section($course, $section) {
        global $DB;

        $section = (object)$section;
        $section->course = $course->id;

        $DB->insert_record('course_sections', $section);

        rebuild_course_cache($course->id, true);
    }

    /**
     * Apply the append rule.
     */
    public function append_to($courses) {
        $sectionident = $this->get_identifier();

        foreach ($courses as $course) {
            $this->datatype->append_to_section($course, $sectionident);
        }
    }

    /**
     * Delete a section.
     */
    public function delete($courses) {
        global $DB;

        // Get the section identifier.
        $sectionident = $this->get_identifier();
        if ($sectionident == 0) {
            throw new \moodle_exception("Cannot delete section 0.");
        }

        // We have a section number, delete that section.
        foreach ($courses as $course) {
            // Get the last section id.
            $lastid = $DB->get_field('course_sections', 'MAX(section)', array(
                'course' => $course->id
            ));

            // Move this to be the last section.
            move_section_to($course, $sectionident, $lastid + 1, true);

            // Get section info, the number of sections won't have changed so we are now $lastid.
            $modinfo = get_fast_modinfo($course);
            $section = $modinfo->get_section_info($lastid);

            // Delete it.
            course_delete_section($course, $section);
        }
    }

    /**
     * Empty out this section.
     */
    public function empty_content($courses) {
        $sectionident = $this->get_identifier();

        // We have a section number, delete that section.
        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course);
            $section = $modinfo->get_section_info($sectionident);
            course_delete_section($course, $section);

            static::create_section($course, array(
                'section' => $section->section,
                'name' => $section->name,
                'visible' => $section->visible,
                'summary' => $section->summary,
                'summaryformat' => $section->summaryformat,
                'availability' => $section->availability
            ));
        }
    }

    /**
     * Apply the prepend rule.
     */
    public function prepend_to($courses) {
        $sectionident = $this->get_identifier();

        foreach ($courses as $course) {
            $this->datatype->prepend_to_section($course, $sectionident);
        }
    }
}
