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
require_once($CFG->dirroot . '/mod/aspirelists/lib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

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
            'aspirelists', 'forum'
        );
    }

    /**
     * Create a course module object.
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
     * Create a forum object.
     */
    private function get_forum($course, $name, $intro) {
        global $DB;

        // Create forum object.
        $instance = new \stdClass();
        $instance->course = $course->id;
        $instance->type = 'general';
        $instance->name = $name;
        $instance->intro = $intro;
        $instance->timemodified = time();
        $instance->id = $DB->insert_record("forum", $instance);

        return $instance;
    }

    /**
     * Create an aspirelists object.
     */
    private function get_aspirelists($course, $name) {
        // Create aspirelists object.
        $instance = new \stdClass();
        $instance->course = $course->id;
        $instance->name = $name;
        $instance->intro = '';
        $instance->introformat = 1;
        $instance->category = 'all';
        $instance->timemodified = time();

        $instance->id = aspirelists_add_instance($instance, null);

        return $instance;
    }

    /**
     * Create everything and return the cm but don't add it to the section.
     */
    private function get_cm($course, $section) {
        global $DB;

        $data = (object)$this->get_data();

        // Get the module.
        $module = $DB->get_record('modules', array(
            'name' => $data->type
        ), '*', \MUST_EXIST);

        // Create our instance.
        $instance = null;
        switch ($data->type) {
            case 'forum':
                $instance = $this->get_forum($course, $data->name, $data->intro);
            break;

            case 'aspirelists':
                $instance = $this->get_aspirelists($course, $data->name);
            break;

            default:
            throw new \moodle_exception('Invalid activity type.');
        }

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
