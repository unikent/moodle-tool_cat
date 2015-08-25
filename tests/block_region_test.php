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
     * Test the region empty rule.
     */
    public function test_block_region_empty() {
        global $DB;

        $generator = $this->getDataGenerator();

        $context = context_course::instance($this->course->id);

        $before = $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*'
        ));

        // Add a block.
        $block = $generator->create_block('online_users', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*'
        ));

        // Ensure the block has been created.
        $this->assertEquals($before + 1, $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*'
        )));

        // Apply a rule to delete the block.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'empty_content',
            'target' => 'block_region',
            'targetid' => \BLOCK_POS_RIGHT,
            'datatype' => '',
            'data' => serialize('')
        ));
        $rule->apply(array($this->course));

        // Ensure the block has been deleted.
        $this->assertEquals(0, $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*'
        )));
    }

    /**
     * Test the region empty rule.
     */
    public function test_block_region_add() {
        global $DB;

        $generator = $this->getDataGenerator();

        $context = context_course::instance($this->course->id);

        $before = $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*'
        ));

        // Apply a rule to append a block.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'block_region',
            'targetid' => \BLOCK_POS_RIGHT,
            'datatype' => 'block',
            'data' => serialize('feedback')
        ));
        $rule->apply(array($this->course));

        // Apply a rule to prepend a block.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 2,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'block_region',
            'targetid' => \BLOCK_POS_RIGHT,
            'datatype' => 'block',
            'data' => serialize('html')
        ));
        $rule->apply(array($this->course));

        // Ensure the html block has a lower weight than the feedback block.
        $feedbackweight = (int)$DB->get_field('block_instances', 'defaultweight', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*',
            'blockname' => 'feedback'
        ));

        $htmlweight = (int)$DB->get_field('block_instances', 'defaultweight', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*',
            'blockname' => 'html'
        ));

        $this->assertLessThan($feedbackweight, $htmlweight);

        // Ensure the block has been created.
        $this->assertEquals($before + 2, $DB->count_records('block_instances', array(
            'parentcontextid' => $context->id,
            'defaultregion' => \BLOCK_POS_RIGHT,
            'pagetypepattern' => 'course-view-*'
        )));
    }
}