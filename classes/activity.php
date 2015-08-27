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

namespace tool_cat;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool activity.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity
{
    private $data;

    /**
     * Constructor.
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Return a list of fields this datatype requires.
     *
     * @return array An array of valid fields.
     */
    public function get_supported_fields() {
        return array(
            'name' => PARAM_TEXT,
            'intro' => PARAM_TEXT
        );
    }

    /**
     * Return a activity object, given a name.
     */
    public static function create_activity($name, $data = '') {
        // Sanity check.
        if (!preg_match('/^([A-Za-z_]*)$/', $name)) {
            throw new \moodle_exception("Invalid activity.");
        }

        $activity = "\\catactivity_{$name}\\{$name}";
        return new $activity($data);
    }

    /**
     * Set our activity data.
     *
     * @param int $data activity data.
     */
    public function set_data($data) {
        $this->data = serialize($data);
    }

    /**
     * Return the data of the activity.
     *
     * @return int The data of the activity.
     */
    public function get_data() {
        return unserialize($this->data);
    }

    /**
     * Create a forum object.
     *
     * @param stdClass $course The course to apply to.
     */
    public abstract function get_instance($course);
}
