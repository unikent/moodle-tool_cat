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
 * Category admin tool activities.
 *
 * @package    catactivity_aspirelists
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catactivity_aspirelists\aspirelists;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/aspirelists/lib.php');

/**
 * Category admin tool aspirelists activity.
 *
 * @package    catactivity_aspirelists
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aspirelists extends \tool_cat\activity
{
    /**
     * Create an aspirelists object.
     *
     * @param stdClass $course The course to apply to.
     */
    public function get_instance($course) {
        $data = (object)$this->get_data();

        // Create aspirelists object.
        $instance = new \stdClass();
        $instance->course       = $course->id;
        $instance->name         = $data->name;
        $instance->intro        = isset($data->intro) ? $data->intro : null;
        $instance->introformat  = 1;
        $instance->category     = 'all';
        $instance->timemodified = time();

        $instance->id = aspirelists_add_instance($instance, null);

        return $instance;
    }
}
