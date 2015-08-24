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

namespace tool_cat\form;

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Category admin tool rule form.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_rules extends \moodleform
{
    /**
     * Form definition
     */
    public function definition() {
        global $CFG, $DB, $PAGE;

        require_once($CFG->libdir. '/coursecatlib.php');

        $PAGE->requires->js_call_amd('tool_cat/form', 'init', array());

        $mform =& $this->_form;

        // Global update button.
        $mform->registerNoSubmitButton('updateform');
        $mform->addElement('submit', 'updateform', 'Update Form', array('class' => 'hidden'));

        // Select a category.
        $mform->addElement('header', 'info', 'Category');
        $catlist = \coursecat::make_categories_list('tool/cat:manage');
        $categories = array(0 => 'Please select a category');
        foreach ($catlist as $k => $v) {
            $categories[$k] = $v;
        }
        $mform->addElement('select', 'category', 'Category', $categories);

        // Do we have a category?
        $category = optional_param('category', false, PARAM_INT);
        if (!empty($category)) {
            $mform->setDefault('category', $category);

            // Populate existing rules.
            $this->add_rule_fieldsets($category);

            // Print a blank rule-add row.
            $mform->addElement('header', 'rules', 'Add a new rule');
            $this->add_rule_fieldset();
        }

        $this->add_action_buttons(true, 'Save rules');
    }

    /**
     * Add existing rule fieldsets.
     */
    private function add_rule_fieldsets($category) {
        global $DB;

        $mform =& $this->_form;

        $rules = \tool_cat\external\rule::get_category_rules($category);

        foreach ($rules as $rule) {
            $mform->addElement('header', "rule_{$rule->id}", 'Rule ' . $rule->id);
            $mform->addElement('hidden', "rule_{$rule->id}_id", $rule->id);
            $mform->setType("rule_{$rule->id}_id", PARAM_INT);

            $this->add_rule_fieldset($rule);
        }
    }

    /**
     * Add a rule fieldset.
     * This might also be a rule in-progress.
     */
    private function add_rule_fieldset($obj = null) {
        $mform =& $this->_form;

        // Where are we?
        $data = isset($obj) ? unserialize($obj->data) : null;
        $rule = isset($obj) ? $obj->rule : optional_param('rule', '', PARAM_ALPHANUMEXT);
        $target = isset($obj) ? $obj->target : optional_param('target', '', PARAM_ALPHANUMEXT);
        $datatype = isset($obj) ? $obj->datatype : optional_param('datatype', '', PARAM_ALPHANUMEXT);
        $activity = is_array($data) && isset($data['activity']) ? $data['activity'] : optional_param('activity', '', PARAM_ALPHANUMEXT);

        // Add a selection box for the rule.
        $validrules = \tool_cat\external\rule::get_rules();
        $mform->addElement('select', 'rule', 'Rule', array_merge(array(0 => 'Select a rule'), $validrules));

        // Do we have a rule?
        if (empty($rule)) {
            return;
        }

        // We have a rule!
        $validtargets = \tool_cat\external\rule::get_targets($rule);
        $mform->addElement('select', 'target', 'Target', array_merge(array(0 => 'Select a target'), $validtargets));
        $mform->addElement('text', 'targetid', 'Target Identifier');
        $mform->setType('targetid', PARAM_TEXT);

        // Do we have a target?
        if (empty($target)) {
            return;
        }

        // We have a target!
        $validdatatypes = \tool_cat\external\rule::get_datatypes($target);
        $mform->addElement('select', 'datatype', 'Data type', array_merge(array(0 => 'Select a data type'), $validdatatypes));

        // Do we have a datatype?
        if (empty($datatype)) {
            return;
        }

        // We might need extra information.
        switch ($datatype) {
            case 'activity':
                $validactivities = \tool_cat\external\rule::get_activities();
                $mform->addElement('select', 'activity', 'Activity', array_merge(array(0 => 'Select an activity'), $validactivities));

                // Do we have an activity submitted?
                if (empty($activity)) {
                    return;
                }

                // Add fields.
                $validfields = \tool_cat\external\rule::get_activity_fields($activity);
                foreach ($validfields as $name => $type) {
                    $mform->addElement('text', $name, ucwords($name));
                    $mform->setType($name, $type);

                    if (isset($obj) && isset($data[$name])) {
                        $mform->setDefault($name, $data[$name]);
                    }
                }
            break;

            case 'block':
                $validblocks = \tool_cat\external\rule::get_blocks();
                $mform->addElement('select', 'block', 'Block', array_merge(array(0 => 'Select a block'), $validblocks));

                $mform->addElement('select', 'blockpos', 'Block Position', array(
                    \BLOCK_POS_LEFT => 'Left',
                    \BLOCK_POS_RIGHT => 'Right'
                ));

                if (isset($obj)) {
                    $mform->setDefault('block', $data['block']);
                    $mform->setDefault('blockpos', $data['blockpos']);
                }
            break;

            case 'section':
                $mform->addElement('text', 'title', 'Section title');
                $mform->setType('title', PARAM_TEXT);

                $mform->addElement('textarea', 'summary', 'Section summary');
                $mform->setType('summary', PARAM_TEXT);

                if (isset($obj)) {
                    $mform->setDefault('title', $data['title']);
                    $mform->setDefault('summary', $data['summary']);
                }
            break;

            case 'text':
            case 'template':
                $mform->addElement('textarea', 'data', 'Data');
                $mform->setType('data', PARAM_TEXT);

                if (isset($obj)) {
                    $mform->setDefault('data', $data);
                }
            break;

            default:
                // All done!
            break;
        }

        if (isset($obj)) {
            $mform->setDefault('rule', $obj->rule);
            $mform->setDefault('target', $obj->target);
            $mform->setDefault('targetid', $obj->targetid);
            $mform->setDefault('datatype', $obj->datatype);
        }
    }

    /**
     * Returns rule data.
     */
    public function get_rules() {
        // TODO.
    }

    /**
     * Returns the data for the selected rule.
     */
    private function get_rule_data($rulenum) {
        // TODO.
    }
}
