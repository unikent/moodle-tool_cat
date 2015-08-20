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

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the category manager
 */
class tool_cat_block_tests extends \advanced_testcase
{
    /**
     * Test the cron.
     */
    public function test_block_delete() {
        // Generate a course.
        // Add some blocks.

        $rule = \tool_cat\rules\base::from_record(array(
            'id' => 1,
            'category' => $course->category,
            'order' => 1,
            'rule' => 'delete',
            'target' => 'block',
            'targetid' => 'html',
            'datatype' => '',
            'data' => ''
        ));
        $rule->apply();

        // Ensure the block has been deleted.
    }
}