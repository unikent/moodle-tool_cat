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

require_once($CFG->libdir . "/blocklib.php");

/**
 * Category admin tool block_region target.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_region extends base
{
    /**
     * Return a list of datatypes this target supports.
     *
     * @return array An array of valid datatypes.
     */
    public function get_supported_datatypes() {
        return array(
            'block'
        );
    }

    /**
     * Returns the block manager for a given course.
     */
    private function get_block_manager($course) {
        $page = new \moodle_page();
        $page->set_context(\context_course::instance($course->id));
        $page->set_pagetype('course-view-*');

        return new \block_manager($page);
    }

    /**
     * Adds a block to the given region for all courses.
     */
    private function add_block($courses, $region, $datatype, $prepend = false) {
        foreach ($courses as $course) {
            $blockmanager = $this->get_block_manager($course);
            if (!$blockmanager->is_known_region($region)) {
                $blockmanager->add_region($region);
            }

            $weight = 0;

            // Move everything out the way, or find the new weight.
            $currentblocks = $blockmanager->get_blocks_for_region($region);
            foreach ($currentblocks as $instance) {
                if ($prepend) {
                    // Find the last position.
                    $weight = $instance->weight + 1;
                    continue;
                }

                // Move this one up.
                $blockmanager->reposition_block($instance->id, $region, $instance->weight + 1);
            }

            // Add the block.
            $blockmanager->add_block($datatype->get_data(), $region, $weight, false);
        }
    }

    /**
     * Apply the append rule.
     */
    public function append_to($courses) {
        $region = $this->get_identifier();
        $datatype = $this->get_datatype();

        // For each course, append the block.
        $this->add_blocks($courses, $region, $datatype);
    }

    /**
     * Delete all blocks in this region.
     */
    public function empty_content($courses) {
        // Our target is the name of a block region.
        $region = $this->get_identifier();

        // For each course, delete all blocks.
        foreach ($courses as $course) {
            $blockmanager = $this->get_block_manager($course);
            if (!$blockmanager->is_known_region($region)) {
                continue;
            }

            $instances = $blockmanager->get_blocks_for_region($region);
            foreach ($instances as $instance) {
                blocks_delete_instance($instance);
            }
        }
    }

    /**
     * Apply the prepend rule.
     */
    public function prepend_to($courses) {
        $region = $this->get_identifier();
        $datatype = $this->get_datatype();

        // For each course, prepend the block.
        $this->add_blocks($courses, $region, $datatype, true);
    }
}
