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
     * Apply the append rule.
     */
    public function append_to($courses) {
        // TODO.
    }

    /**
     * Apply the prepend rule.
     */
    public function prepend_to($courses) {
        // TODO.
    }

    /**
     * Apply the delete rule.
     */
    public function delete($courses) {
        // TODO.
    }

    /**
     * Apply the empty content rule.
     */
    public function empty_content($courses) {
        // TODO.
    }
}
