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
 * @package    catdatatype_activity
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catdatatype_activity;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

/**
 * Category admin tool activity data type.
 *
 * @package    catdatatype_activity
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity extends \tool_cat\datatype
{
    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public function get_supported_activities() {
        $pluginman = \core_plugin_manager::instance();
        $plugins = $pluginman->get_plugins_of_type('catactivity');

        $rules = array();
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() === false) {
                continue;
            }

            $rules[] = $plugin->name;
        }

        return $rules;
    }

    /**
     * Create a course module object.
     *
     * @param  stdClass $course     The course to apply to.
     * @param  stdClass $section    The section to apply to.
     * @param  stdClass $module     The module to apply to.
     * @param  stdClass $instance   The instance to apply to.
     * @param  stdClass $data       The original data we extracted.
     */
    private function create_cm($course, $section, $module, $instance, $data) {
        // Create a module container.
        $cm = new \stdClass();
        $cm->course     = $course->id;
        $cm->module     = $module->id;
        $cm->instance   = $instance->id;
        $cm->section    = $section->id;
        $cm->visible    = 1;

        if (isset($data->showdescription)) {
            $cm->showdescription = $data->showdescription;
        }

        // Create the module.
        $cm->id = add_course_module($cm);

        return $cm;
    }

    /**
     * Get data (CM).
     */
    public function get_data() {
        global $DB;

        $context = $this->get_context();
        if (!is_array($context) || !isset($context['course']) || !isset($context['section'])) {
            throw new \moodle_exception("Invalid activity datatype context!");
        }

        $data = parent::get_data();

        // Get the module.
        $module = $DB->get_record('modules', array(
            'name' => $data->activity
        ), '*', \MUST_EXIST);

        // Create our instance.
        $activity = \tool_cat\activity::create_activity($data->activity, serialize($data));
        $instance = $activity->get_instance($context['course']);

        // Create the cm.
        return $this->create_cm($context['course'], $context['section'], $module, $instance, $data);
    }
}
