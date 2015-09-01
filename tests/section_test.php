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

        // Apply a rule to append to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'text',
            'data' => serialize((object)array('text' => $sectiontext))
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

        // Apply a rule to prepend to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'text',
            'data' => serialize((object)array('text' => $sectiontext))
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

        // Apply a rule to append to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'text',
            'data' => serialize((object)array('text' => $sectiontext, 'template' => true))
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

        // Apply a rule to prepend to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'text',
            'data' => serialize((object)array('text' => $sectiontext, 'template' => true))
        ));
        $rule->apply(array($this->course));

        $this->assertEquals($expected . $original, $DB->get_field('course_sections', 'summary', array(
            'course' => $this->course->id,
            'section' => 1
        )));
    }

    /**
     * Test the section append rule on the forum activity.
     */
    public function test_section_forum() {
        global $DB;

        $this->assertEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 1
        )));

        // Apply a rule to append to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'activity',
            'data' => serialize(array(
                'activity' => 'forum',
                'name' => 'Test forum',
                'intro' => 'This is a test forum'
            ))
        ));
        $rule->apply(array($this->course));

        // Apply a rule to prepend to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'activity',
            'data' => serialize(array(
                'activity' => 'forum',
                'name' => 'First forum',
                'intro' => 'This is the most important forum'
            ))
        ));
        $rule->apply(array($this->course));

        $this->assertNotEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 1
        )));

        // Get modinfo.
        $modinfo = get_fast_modinfo($this->course);
        $section = $modinfo->get_section_info(1);
        $seq = explode(',', $section->sequence);

        // Make sure seq[0] name is "First forum".
        $cm = $modinfo->cms[$seq[0]];

        $this->assertEquals('First forum', $cm->name);
    }

    /**
     * Test the section append rule on the aspirelists activity.
     */
    public function test_section_aspirelists() {
        global $DB;

        $this->assertEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 1
        )));

        // Apply a rule to append to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'activity',
            'data' => serialize(array(
                'activity' => 'aspirelists',
                'name' => 'Reading List'
            ))
        ));
        $rule->apply(array($this->course));

        // Apply a rule to prepend to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'activity',
            'data' => serialize(array(
                'activity' => 'aspirelists',
                'name' => 'Top 10 Reading Lists 2015'
            ))
        ));
        $rule->apply(array($this->course));

        $this->assertNotEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 1
        )));

        // Get modinfo.
        $modinfo = get_fast_modinfo($this->course);
        $section = $modinfo->get_section_info(1);
        $seq = explode(',', $section->sequence);

        // Make sure seq[0] name is the prepended one.
        $cm = $modinfo->cms[$seq[0]];

        $this->assertEquals('Top 10 Reading Lists 2015', $cm->name);
    }

    /**
     * Test the section append rule on the url activity.
     */
    public function test_section_url() {
        global $DB;

        $this->assertEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 1
        )));

        // Apply a rule to append to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'append_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'activity',
            'data' => serialize(array(
                'activity' => 'url',
                'name' => 'URL',
                'url' => 'http://www.google.com'
            ))
        ));
        $rule->apply(array($this->course));

        // Apply a rule to prepend to the section.
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
            'order' => 1,
            'rule' => 'prepend_to',
            'target' => 'section',
            'targetid' => '1',
            'datatype' => 'activity',
            'data' => serialize(array(
                'activity' => 'url',
                'name' => 'Main URL',
                'url' => 'http://www.google.com'
            ))
        ));
        $rule->apply(array($this->course));

        $this->assertNotEmpty($DB->get_field('course_sections', 'sequence', array(
            'course' => $this->course->id,
            'section' => 1
        )));

        // Get modinfo.
        $modinfo = get_fast_modinfo($this->course);
        $section = $modinfo->get_section_info(1);
        $seq = explode(',', $section->sequence);

        // Make sure seq[0] name is the prepended one.
        $cm = $modinfo->cms[$seq[0]];

        $this->assertEquals('Main URL', $cm->name);
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
        $rule = \tool_cat\rule::from_record(array(
            'id' => \tool_cat\rule::FAKE_RULE_ID,
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