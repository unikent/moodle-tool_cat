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

/**
 * Category admin tool data type.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base
{
    private $data;

    /**
     * Constructor.
     */
    public function __construct($data) {
        $this->set_data($data);
    }

    /**
     * Set our datatype data.
     *
     * @param int $data datatype data.
     */
    public function set_data($data) {
        $this->data = $data;
    }

    /**
     * Return the data of the datatype.
     *
     * @return int The data of the datatype.
     */
    public function get_data() {
        return $this->data;
    }
}
