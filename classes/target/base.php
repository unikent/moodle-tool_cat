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

namespace tool_cat;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool target.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base
{
    protected $identifier;

    /**
     * Set our target identifier.
     *
     * @param int $identifier Target identifier.
     */
    public function set_identifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Return the identifier of the target.
     *
     * @return int The identifier of the target.
     */
    public function get_identifier() {
        return $this->identifier;
    }

    /**
     * Return a list of rules this target supports.
     *
     * @return array An array of valid rules.
     */
    public abstract function get_supported_rules();

    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public abstract function get_supported_datatypes();
}
