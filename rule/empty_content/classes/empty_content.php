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
 * @package    catrule_empty_content
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catrule_empty_content;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool empty_content rule.
 * Deletes all content from a target.
 *
 * @package    catrule_empty_content
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class empty_content extends \tool_cat\rule
{
    /**
     * Return a list of targets this rule supports.
     *
     * @return array An array of valid targets.
     */
    public function get_supported_targets() {
        return array(
            'section', 'block_region'
        );
    }

    /**
     * Apply the rule.
     *
     * @param array $courses An array of courses to apply to rule to.
     * @return array An array of courses we applied ourselves to.
     */
    protected function _apply($courses) {
        $this->target->empty_content($courses);

        return $courses;
    }
}
