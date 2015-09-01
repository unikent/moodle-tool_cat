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

namespace tool_cat;

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
    const FAKE_RULE_ID = -1;
    protected $target;

    /**
     * Return a rule object, given a name.
     */
    public static function create_rule($name) {
        // Sanity check.
        $pluginman = \core_plugin_manager::instance();
        $plugins = $pluginman->get_plugins_of_type('catrule');
        if (!isset($plugins[$name]) || $plugins[$name]->is_enabled() === false) {
            throw new \moodle_exception("Invalid rule.");
        }

        $ruletype = "\\catrule_{$name}\\{$name}";
        return new $ruletype();
    }

    /**
     * Create a new rule object from record.
     */
    public static function from_record($record) {
        $record = (object)$record;

        $obj = static::create_rule($record->rule);
        $obj->id = $record->id;
        $obj->target = \tool_cat\target::create_target($record->target, $record->targetid);

        // Add a datatype to the rule if we have one.
        if (!empty($record->datatype)) {
            $datatype = \tool_cat\datatype::create_datatype($record->datatype, $record->data);
            $obj->target->set_datatype($datatype);
        }

        return $obj;
    }

    /**
     * Returns true if this is a "fake" rule (not in DB).
     */
    protected final function is_fake() {
        return $this->id == static::FAKE_RULE_ID;
    }

    /**
     * Apply the rule.
     *
     * @param array $courses An array of courses to apply to rule to.
     */
    public final function apply($courses) {
        global $DB;

        // Remove all courses that have already applied this rule.
        $intersect = $DB->get_fieldset_select('tool_cat_applications', 'courseid', 'ruleid=:ruleid', array(
            'ruleid' => $this->id
        ));

        if (!empty($intersect)) {
            foreach ($courses as $k => $course) {
                if (in_array($course->id, $intersect)) {
                    unset($courses[$k]);
                }
            }
        }

        unset($intersect);

        // Apply.
        $done = $this->_apply($courses);

        // If this is a fake rule, stop now.
        if ($this->is_fake()) {
            return;
        }

        // Trigger events.
        $appliedbuffer = array();
        foreach ($done as $course) {
            // Trigger a rule applied event.
            $event = \tool_cat\event\rule_applied::create(array(
                'objectid' => $this->id,
                'courseid' => $course->id,
                'context' => \context_course::instance($course->id)
            ));
            $event->trigger();

            $appliedbuffer[] = array(
                'courseid' => $course->id,
                'ruleid' => $this->id
            );
        }

        unset($done);
        $DB->insert_records('tool_cat_applications', $appliedbuffer);
    }

    /**
     * Return a list of targets this rule supports.
     *
     * @return array An array of valid targets.
     */
    public abstract function get_supported_targets();

    /**
     * Apply the rule.
     *
     * @param array $courses An array of courses to apply to rule to.
     * @return array An array of courses we applied ourselves to.
     */
    protected abstract function _apply($courses);
}
