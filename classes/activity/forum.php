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
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cat\activity;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool forum activity.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum extends base
{
    /**
     * Create a forum object.
     *
     * @param  stdClass $course  The course to apply to.
     */
    public function get_instance($course) {
        global $DB;

        $data = (object)$this->get_data();

        // Create forum object.
        $instance = new \stdClass();
        $instance->course       = $course->id;
        $instance->type         = 'general';
        $instance->name         = $data->name;
        $instance->intro        = isset($data->intro) ? $data->intro : null;
        $instance->timemodified = time();

        $instance->id = $DB->insert_record("forum", $instance);

        return $instance;
    }
}
