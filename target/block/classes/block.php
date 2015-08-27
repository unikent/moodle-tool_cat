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
 * @package    cattarget_block
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cattarget_block;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool block target.
 *
 * @package    cattarget_block
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block extends \tool_cat\target
{
    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public function get_supported_datatypes() {
        return array();
    }

    /**
     * Delete method.
     *
     * @param array $courses All courses we should be effecting.
     */
    public function delete($courses) {
        global $CFG;

        require_once($CFG->libdir . "/blocklib.php");

        // We now want to delete all blocks with that name.
        $instances = $this->get_instances($courses);
        foreach ($instances as $instance) {
            blocks_delete_instance($instance);
        }
    }

    /**
     * Return all instances of a block for the given courses.
     */
    private function get_instances($courses) {
        global $DB;

        // Our target is the name of a block.
        $block = $this->get_identifier();

        // Build instance list.
        $instances = array();
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            $courseinstances = $DB->get_records('block_instances', array(
                'blockname' => $block,
                'parentcontextid' => $context->id
            ));

            $instances = array_merge($courseinstances, $instances);
        }

        return $instances;
    }
}
