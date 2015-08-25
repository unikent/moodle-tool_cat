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
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cat\datatype;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

/**
 * Category admin tool activity data type.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity extends base
{
    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public function get_supported_activities() {
        return array(
            'aspirelists', 'forum', 'url'
        );
    }

    /**
     * Create a course module object.
     *
     * @param  stdClass $course     The course to apply to.
     * @param  stdClass $section    The section to apply to.
     * @param  stdClass $module     The module to apply to.
     * @param  stdClass $instance   The instance to apply to.
     */
    private function create_cm($course, $section, $module, $instance) {
        // Create a module container.
        $cm = new \stdClass();
        $cm->course     = $course->id;
        $cm->module     = $module->id;
        $cm->instance   = $instance->id;
        $cm->section    = $section->id;
        $cm->visible    = 1;

        // Create the module.
        $cm->id = add_course_module($cm);

        return $cm;
    }

    /**
     * Create everything and return the cm but don't add it to the section.
     *
     * @param  stdClass $course   The course to apply to.
     * @param  stdClass $section  The section to apply to.
     */
    private function get_cm($course, $section) {
        global $DB;

        $data = (object)$this->get_data();

        // Get the module.
        $module = $DB->get_record('modules', array(
            'name' => $data->activity
        ), '*', \MUST_EXIST);

        // Create our instance.
        $activity = \tool_cat\activity\base::create_activity($data->activity, serialize($data));
        $instance = $activity->get_instance($course);

        // Create the cm.
        return $this->create_cm($course, $section, $module, $instance);
    }

    /**
     * Append an activity to the given course/section.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     */
    public function append_to_section($course, $sectionident) {
        global $DB;

        // Find the section.
        $section = $DB->get_record('course_sections', array(
            'course' => $course->id,
            'section' => $sectionident
        ));

        $cm = $this->get_cm($course, $section);
        course_add_cm_to_section($course->id, $cm->id, $section->section);
    }

    /**
     * Prepend an activity to the given course/section.
     *
     * @param  stdClass $course        The course to apply to.
     * @param  int      $sectionident  The section number to apply to (not ID).
     */
    public function prepend_to_section($course, $sectionident) {
        global $DB;

        // Find the section.
        $section = $DB->get_record('course_sections', array(
            'course' => $course->id,
            'section' => $sectionident
        ));

        // Find the first element in the section.
        $pos = null;
        $seq = explode(',', $section->sequence);
        if (!empty($seq)) {
            $pos = reset($seq);
        }

        $cm = $this->get_cm($course, $section);
        course_add_cm_to_section($course->id, $cm->id, $section->section, $pos);
    }
}
