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
        $categories = \coursecat::make_categories_list('tool/cat:manage');
        $mform->addElement('select', 'category', 'Category', array_merge(array('0' => 'Please select a category'), $categories));

        $category = optional_param('category', false, PARAM_INT);

        // Do we have a category?
        if (!empty($category)) {
            // Populate existing rules.
            $this->add_rule_fieldsets($category);

            // Print a blank rule-add row.
            $this->add_blank_rule();
        }

        $this->add_action_buttons(true, 'Add rule');
    }

    /**
     * Add existing rule fieldsets.
     */
    private function add_rule_fieldsets($category) {
        global $DB;

        $mform =& $this->_form;

        $rules = \tool_cat\external\rule::get_category_rules($category);

        foreach ($rules as $rule) {
            $rule = \tool_cat\rule\base::from_record($rule);

            $mform->addElement('header', 'rule' . $rule->id, 'Rule ' . $rule->id);
            // TODO.
        }
    }

    /**
     * Add a blank fieldset.
     * This might also be a rule in-progress.
     */
    private function add_blank_rule() {
        $mform =& $this->_form;

        $mform->addElement('header', 'rules', 'Add a new rule');

        // Where are we?
        $rule = optional_param('rule', '', PARAM_ALPHANUMEXT);
        $target = optional_param('target', '', PARAM_ALPHANUMEXT);
        $datatype = optional_param('datatype', '', PARAM_ALPHANUMEXT);

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
                $mform->addElement('select', 'activity', 'Activity', array_merge(array(0 => 'Select a activity'), $validactivities));
            // TODO - select activity, name.
            break;

            case 'block':
            // TODO - list of blocks to add, position.
            break;

            case 'section':
                $mform->addElement('text', 'title', 'Section title');
                $mform->setType('title', PARAM_TEXT);

                $mform->addElement('textarea', 'summary', 'Section summary');
                $mform->setType('summary', PARAM_TEXT);
            break;

            case 'text':
            case 'template':
                $mform->addElement('textarea', 'data', 'Data');
                $mform->setType('data', PARAM_TEXT);
            break;

            default:
                // All done!
            break;
        }
    }
}
