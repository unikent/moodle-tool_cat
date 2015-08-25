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
class tool_cat_course_tests extends \advanced_testcase
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
        course_create_sections_if_missing($this->course, array(1, 2, 3, 4, 5, 6, 7, 8));
    }

    /**
     * Return a fake section.
     */
    private function generate_sectiondata() {
        return array(
            'name' => 'Test section.',
            'visible' => 1,
            'summary' => '',
            'summaryformat' => 1
        );
    }

    /**
     * Test the course append rule.
     */
    public function test_course_append_news() {
        global $DB;

        $this->assertEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 0
        )));

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'course',
            'targetid' => $this->course->id,
            'datatype' => 'news',
            'data' => serialize('')
        ));
        $rule->apply(array($this->course));

        // Ensure the forum has been created.
        $this->assertNotEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 0
        )));
    }

    /**
     * Test the course append rule.
     */
    public function test_course_append() {
        global $DB;

        $before = $DB->count_records('course_sections', array(
            'course' => $this->course->id
        ));

        $this->assertEquals(9, $before);

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'course',
            'targetid' => $this->course->id,
            'datatype' => 'section',
            'data' => serialize($this->generate_sectiondata())
        ));
        $rule->apply(array($this->course));

        // Ensure the section has been created.
        $this->assertEquals($before + 1, $DB->count_records('course_sections', array(
            'course' => $this->course->id
        )));
    }

    /**
     * Test the course prepend rule.
     */
    public function test_course_prepend() {
        global $DB;

        $before = $DB->count_records('course_sections', array(
            'course' => $this->course->id
        ));

        $this->assertEquals(9, $before);

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'course',
            'targetid' => $this->course->id,
            'datatype' => 'section',
            'data' => serialize($this->generate_sectiondata())
        ));
        $rule->apply(array($this->course));

        // Ensure the section has been created.
        $this->assertEquals($before + 1, $DB->count_records('course_sections', array(
            'course' => $this->course->id
        )));
    }
}