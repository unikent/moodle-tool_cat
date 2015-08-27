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
    /** @var stdClass Keeps course object */
    private $course;

    /**
     * Setup test data.
     */
    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and wiki.
        $this->course = $this->getDataGenerator()->create_course();
    }

    /**
     * Test the block delete rule.
     */
    public function test_block_delete() {
        global $DB;

        $generator = $this->getDataGenerator();

        $context = context_course::instance($this->course->id);

        $before = $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id
        ));

        // Add a block.
        $block = $generator->create_block('online_users', array(
            'parentcontextid' => $context->id
        ));

        // Ensure the block has been created.
        $this->assertEquals($before + 1, $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id
        )));

        // Apply a rule to delete the block.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'delete',
            'target' => 'block',
            'targetid' => 'online_users',
            'datatype' => '',
            'data' => serialize('')
        ));
        $rule->apply(array($this->course));

        // Ensure the block has been deleted.
        $this->assertEquals($before, $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id
        )));
    }
}