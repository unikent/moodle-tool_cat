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
 * @package    cattarget_section
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cattarget_section;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/lib.php");

/**
 * Category admin tool section target.
 *
 * @package    cattarget_section
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section extends \tool_cat\target
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
     * Apply the append rule.
     */
    public function append_to($courses) {
        $sectionident = $this->get_identifier();

        $type = 'text_append_to';
        if ($this->datatype->get_name() == 'activity') {
            $type = 'activity_append_to';
        }

        foreach ($courses as $course) {
            $this->$type($course, $sectionident);
        }
    }

    /**
     * Apply the append rule.
     */
    public function activity_append_to($course, $sectionident) {
        $this->activity_helper($course, $sectionident, false);
    }

    /**
     * Apply the append rule.
     */
    public function text_append_to($course, $sectionident) {
        $this->datatype->set_context($course);
        $text = $this->get_section_text($course, $sectionident);
        $text .= $this->datatype->get_data();
        $this->set_section_text($course, $sectionident, $text);
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

        $type = 'text_prepend_to';
        if ($this->datatype->get_name() == 'activity') {
            $type = 'activity_prepend_to';
        }

        foreach ($courses as $course) {
            $this->$type($course, $sectionident);
        }
    }

    /**
     * Apply the prepend rule.
     */
    public function activity_prepend_to($course, $sectionident) {
        $this->activity_helper($course, $sectionident, true);
    }

    /**
     * Apply the prepend rule.
     */
    public function text_prepend_to($course, $sectionident) {
        $this->datatype->set_context($course);
        $text = $this->datatype->get_data();
        $text .= $this->get_section_text($course, $sectionident);
        $this->set_section_text($course, $sectionident, $text);
    }

    /**
     * Helper for the activity append/prepends.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     */
    private function activity_helper($course, $sectionident, $prepend = false) {
        global $DB;

        // Find the section.
        $section = $DB->get_record('course_sections', array(
            'course' => $course->id,
            'section' => $sectionident
        ));

        // Find the first element in the section.
        $pos = null;
        if ($prepend) {
            $seq = explode(',', $section->sequence);
            if (!empty($seq)) {
                $pos = reset($seq);
            }
        }

        // Get the CM, add it.
        $this->datatype->set_context(array(
            'course' => $course,
            'section' => $section
        ));
        $cm = $this->datatype->get_data();
        course_add_cm_to_section($course->id, $cm->id, $section->section, $pos);
    }
}
