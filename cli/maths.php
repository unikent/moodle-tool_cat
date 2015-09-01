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
 * Special maths CLI tool.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Get a list of courses to rollover.
$courses = $DB->get_records_sql('SELECT * FROM {course} WHERE (shortname LIKE "MA%" AND shortname NOT LIKE "MAE%") OR shortname = \'WSHOPMA1\'');

$sections = array(
    array(
        'name' => 'Past exam papers',
        'visible' => 0,
        'summary' => '<p>Past exam papers for this module may be located using the following link. <em>(Update the link as appropriate)</em></p><p><em>Include here any warnings, such as syllabus changes, etc., and any advice for students on how to use these exam papers.</em></p><p>Worked solutions to six section A-style questions and four section B-style questions will be made available.&nbsp;<em>Edit this section to indicate how. (</em><em>The solutions&nbsp;could be typed or legibly handwritten and given out in hardcopy or posted here, notes made using an active panel, KentPlayer recordings, etc. </em><em>You are advised against posting the solutions to a full exam paper&nbsp;on moodle&nbsp;in case the solutions contain errors.)</em></p>',
        'summaryformat' => \FORMAT_HTML
    ),
    array(
        'name' => 'Assessments',
        'visible' => 0,
        'summary' => '<p><em>Add assessments&nbsp;as topics</em></p><p><em>Provide generic feedback in files (optional)</em><br></p>',
        'summaryformat' => \FORMAT_HTML
    ),
    array(
        'name' => 'Assessment',
        'visible' => 0,
        'summary' => '<p><strong>Method of assessment</strong></p><p><em>Provide information on how the module is assessed, e.g. </em><em>This module is assessed&nbsp;by a 2-hour examination in the Summer Term (contributing 80% of the module mark) and&nbsp;assessed coursework (contributing 20% of the module mark).</em><br></p><p><strong></strong><strong> </strong><strong> </strong><strong> </strong><strong> </strong></p><table> <caption><strong>Coursework Assessments</strong></caption><thead><tr><th scope="col">Task</th><th scope="col">Date to be set</th><th scope="col">Deadline</th></tr></thead><tbody><tr><td><em>e.g. Exercise Sheet 1, questions 1, 4, 5</em></td><td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td><td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td></tr><tr><td><em>e.g. Exercise Sheet 2, questions 1, 2, 3</em></td><td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td><td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td></tr></tbody></table><p><em>Make sure that the deadline dates are included on SDS.<strong>&nbsp;</strong></em></p><p><strong>Feedback on assessed&nbsp;coursework</strong></p><p><em>Provide brief details here on how feedback will be provided, e.g. written comments on scripts, summary feedback on moodle, worked solutions distributed in hard copy only or posted on moodle, solutions covered in class, recorded solutions (KentPlayer), etc.</em></p><p><strong>Assessments</strong><br></p><p><em>The way in which&nbsp;assessments are presented below is at your discretion. They can either be added here in a single list (using clear titles and indenting as appropriate) or added in separate \'topic\' sections below. </em><em>If class-wide summary feedback is provided, it should appear in this section. </em><em>Only this \'Assessments\' section or the \'topic\' section(s) below&nbsp;is required. <br></em></p>',
        'summaryformat' => \FORMAT_HTML
    ),
    array(
        'name' => 'Coursework (or alternative structure)',
        'visible' => 0,
        'summary' => '<p>This coursework is not assessed.<em> </em></p><p><em>Delete (or alternative structure) above.</em></p>',
        'summaryformat' => \FORMAT_HTML
    ),
    array(
        'name' => 'Lecture Material (or alternative structure)',
        'visible' => 0,
        'summary' => '<p><em>Delete (or alternative structure) above.</em><br></p>',
        'summaryformat' => \FORMAT_HTML
    ),
    array(
        'name' => 'Teaching',
        'visible' => 0,
        'summary' => '<p><strong>Delivery</strong></p><p><em>Provide information on the use being made of lectures, classes, etc,&nbsp;and formative (unassessed) coursework tasks. (Formative coursework tasks are optional, but details should be included below if any are set.) Timetable details should not be included.</em></p><p>
</p><table>
<caption><strong>Unassessed Coursework</strong></caption><thead><tr><th scope="col">Task</th><th scope="col">Date to be set</th><th scope="col">Deadline</th>
</tr>
</thead>
<tbody>
<tr>
<td><em>e.g. Exercise Sheet 1, questions 1, 4, 5</em></td>
<td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td>
<td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td>
</tr>
<tr>
<td><em>e.g. Exercise Sheet 2, questions 1, 2, 3</em></td>
<td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td>
<td><p><em>dd/mm/yy&nbsp;</em></p><p><em>or ****day, Week **</em></p></td>
</tr>
</tbody>
</table><p></p><p><strong>
</strong><em>Make sure that the deadline dates are included on SDS.<strong>&nbsp;</strong></em></p><p><strong>Feedback on unassessed coursework</strong></p><p><em>Provide brief details here on how feedback will be provided, e.g. written comments on scripts, peer review and feedback, summary feedback on moodle, worked solutions distributed in hard copy only or posted on moodle, solutions covered in class, recorded solutions (KentPlayer), etc.</em></p><p><em></em><strong>Resources</strong><br></p><p><em>The way in which resources are presented below is at your discretion, so by type, topic, week, etc. The titles should be descriptive of the content. Resources can either be added here in a single list (using indenting as appropriate) or added in separate \'topic\' sections below. Only this \'Resources\' section or the \'topic\' section(s) below&nbsp;is required.</em></p><p><em>The material should include</em></p><ul><li><div><em>lecture notes (or a means to recreate them), either uploaded before or after the lecture, and may be typed or legible handwriting, notes from the active panel made during the lecture, reference to a textbook where this is being followed closely, a copy of a reliable student\'s notes (lecturer responsible for accuracy), KentPlayer recordings, etc.</em></div></li><li><div><em>copies of handouts (if applicable)</em></div></li><li><div><em>formative exercises/quizzes if set</em></div></li><li><div><em>worked solutions to all formative exercises (which may, alternatively, be made available in hardcopy only)</em></div></li><li><div><em>(optional) class-wide feedback on formative exercises.</em></div></li></ul>',
        'summaryformat' => \FORMAT_HTML
    )
);

// Append 6 new sections.
for ($i = 0; $i < 6; $i++) {
    $rule = \tool_cat\rule::from_record(array(
        'id' => \tool_cat\rule::FAKE_RULE_ID,
        'order' => 1,
        'rule' => 'prepend_to',
        'target' => 'course',
        'targetid' => null,
        'datatype' => 'section',
        'data' => serialize($sections[$i])
    ));
    $rule->apply($courses);
}

// Add a new bit of text to the summary of the first section.
$summary = <<<HTML5
<p style="margin: 0cm 0cm 10pt 72pt; text-indent: -72pt;"><em>All text in italics should be deleted before the module is made available to students.</em></p><p style="margin: 0cm 0cm 10pt 72pt; text-indent: -72pt;"><b>Synopsis</b></p><p><em>

Copied from the Module Guide</em></p><p><strong>Use of Moodle</strong></p><p><em>Key information on how moodle will be used to support the module, e.g. when material will be added to moodle, whether Announcments will be used, whether the discussion forum will be used, etc.</em></p><table><caption><strong>Contact Details<br></strong></caption><thead><tr><th scope="col"></th><th scope="col">Name</th><th scope="col">Email address</th><th scope="col">Telephone</th><th scope="col">Office Hours</th></tr></thead><tbody><tr><td><strong>Module Convenor</strong></td>
<td>************</td>
<td><a href="mailto:***@kent.ac.uk">***@kent.ac.uk</a></td>
<td>01227 82****</td>
<td><p><em>Day, time</em></p><p><em>Day, time</em></p></td>
</tr><tr>
<td><strong>Lecturer</strong></td>
<td>************</td>
<td><a href="mailto:***@kent.ac.uk">***@kent.ac.uk</a></td>
<td>01227 82****</td>
<td><p><em>Day, time</em></p><p><em>Day, time</em></p></td>
</tr>
<tr>
<td><strong>Lecturer</strong></td>
<td>************</td>
<td><a href="mailto:***@kent.ac.uk">***@kent.ac.uk</a></td>
<td>01227 82****</td>
<td><p><em>Day, time</em></p><p><em>Day, time</em></p></td></tr></tbody></table><em>
</em>
HTML5;
$rule = \tool_cat\rule::from_record(array(
    'id' => \tool_cat\rule::FAKE_RULE_ID,
    'order' => 1,
    'rule' => 'prepend_to',
    'target' => 'section',
    'targetid' => 0,
    'datatype' => 'text',
    'data' => serialize(array(
        'text' => $summary
    ))
));
$rule->apply($courses);

// Apply first section.
foreach ($courses as $course) {
    // We can't use a rule for this.
    $DB->set_field('course_sections', 'name', get_course_display_name_for_list($course), array(
        'course' => $course->id,
        'section' => 0
    ));

    // Prepend an announcements forum.
    $rule = \tool_cat\rule::from_record(array(
        'id' => \tool_cat\rule::FAKE_RULE_ID,
        'order' => 1,
        'rule' => 'append_to',
        'target' => 'course',
        'targetid' => null,
        'datatype' => 'news',
        'data' => serialize(array(
            'intro' => 'Announcements relating to ' . $course->shortname . ' will be posted here.',
            'showdescription' => 1
        ))
    ));
    $rule->apply(array($course));

    // Append a discussion forum.
    $rule = \tool_cat\rule::from_record(array(
        'id' => \tool_cat\rule::FAKE_RULE_ID,
        'order' => 1,
        'rule' => 'append_to',
        'target' => 'section',
        'targetid' => '0',
        'datatype' => 'activity',
        'data' => serialize(array(
            'activity' => 'forum',
            'name' => 'Discussion Forum ',
            'intro' => '<p>(optional) Use this Discussion Forum to discuss the material of ' . $course->shortname . ' with other students and/or staff.</p><p>Please note that posts seeking (or providing) direct solutions to coursework, either assessed or unassessed, are not permitted.</p>',
            'showdescription' => 1
        ))
    ));
    $rule->apply(array($course));
}

// Apply a rule to prepend to the section.
$rule = \tool_cat\rule::from_record(array(
    'id' => \tool_cat\rule::FAKE_RULE_ID,
    'order' => 1,
    'rule' => 'prepend_to',
    'target' => 'section',
    'targetid' => '0',
    'datatype' => 'activity',
    'data' => serialize(array(
        'activity' => 'aspirelists',
        'name' => 'Reading list'
    ))
));
$rule->apply($courses);

// Append a URL mod to the 7th section.
$rule = \tool_cat\rule::from_record(array(
    'id' => \tool_cat\rule::FAKE_RULE_ID,
    'order' => 1,
    'rule' => 'append_to',
    'target' => 'section',
    'targetid' => '6',
    'datatype' => 'activity',
    'data' => serialize(array(
        'activity' => 'url',
        'name' => 'Past exam papers Link',
        'url' => ''
    ))
));
$rule->apply($courses);

// So we can work something out.
print_r(array_keys($courses));