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
class tool_cat_section_tests extends \advanced_testcase
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
        course_create_sections_if_missing($this->course, array(1, 2, 3, 4, 5, 6, 7, 8, 9));
    }

    /**
     * Test the section append rule.
     */
    public function test_section_append_text() {
        global $DB;

        $original = 'TEST';
        $sectiontext = "I've seen westerns, I know how to speak cowboy.";

        $DB->set_field('course_sections', 'summary', $original, array(
            'course' => $this->course->id,
            'section' => 1
        ));

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'text',
            'data' => serialize($sectiontext)
        ));
        $rule->apply(array($this->course));

        $this->assertEquals($original . $sectiontext, $DB->get_field('course_sections', 'summary', array(
            'course' => $this->course->id,
            'section' => 1
        )));
    }

    /**
     * Test the section append rule.
     */
    public function test_section_prepend_text() {
        global $DB;

        $original = 'TEST';
        $sectiontext = "Proper dumplings should not bounce.";

        $DB->set_field('course_sections', 'summary', $original, array(
            'course' => $this->course->id,
            'section' => 1
        ));

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'text',
            'data' => serialize($sectiontext)
        ));
        $rule->apply(array($this->course));

        $this->assertEquals($sectiontext . $original, $DB->get_field('course_sections', 'summary', array(
            'course' => $this->course->id,
            'section' => 1
        )));
    }

    /**
     * Test the section append rule.
     */
    public function test_section_append_template() {
        global $DB;

        $original = 'TEST';
        $sectiontext = "{{shortname}} is about maths.";
        $expected = $this->course->shortname . ' is about maths.';

        $DB->set_field('course_sections', 'summary', $original, array(
            'course' => $this->course->id,
            'section' => 1
        ));

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'template',
            'data' => serialize($sectiontext)
        ));
        $rule->apply(array($this->course));

        $this->assertEquals($original . $expected, $DB->get_field('course_sections', 'summary', array(
            'course' => $this->course->id,
            'section' => 1
        )));
    }

    /**
     * Test the section append rule.
     */
    public function test_section_prepend_template() {
        global $DB;

        $original = 'TEST';
        $sectiontext = "{{shortname}} is about maths.";
        $expected = $this->course->shortname . ' is about maths.';

        $DB->set_field('course_sections', 'summary', $original, array(
            'course' => $this->course->id,
            'section' => 1
        ));

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'template',
            'data' => serialize($sectiontext)
        ));
        $rule->apply(array($this->course));

        $this->assertEquals($expected . $original, $DB->get_field('course_sections', 'summary', array(
            'course' => $this->course->id,
            'section' => 1
        )));
    }

    /**
     * Test the section delete rule.
     */
    public function test_section_delete() {
        global $DB;

        $before = $DB->count_records('course_sections', array(
            'course' => $this->course->id
        ));

        $this->assertEquals(10, $before);

        // Apply a rule to delete the section.
        $rule = \tool_cat\rule\base::from_record(array(
            'id' => 1,
            'order' => 1,
            'rule' => 'delete',
            'target' => 'section',
            'targetid' => '3',
            'datatype' => '',
            'data' => serialize('')
        ));
        $rule->apply(array($this->course));

        // Ensure the section has been deleted.
        $this->assertEquals($before - 1, $DB->count_records('course_sections', array(
            'course' => $this->course->id
        )));
    }
}