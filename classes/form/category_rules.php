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
        $mform->addElement('select', 'categoryid', 'Category', $categories);

        // Do we have a category?
        $category = optional_param('categoryid', false, PARAM_INT);
        if (!empty($category)) {
            $mform->setDefault('categoryid', $category);

            // Populate existing rules.
            $this->add_rule_fieldsets($category);

            // Print a blank rule-add row.
            $mform->addElement('header', 'rule_new', 'Add a new rule');
            $mform->setExpanded('rule_new');
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

        $rules = \tool_cat\external::get_category_rules($category);

        foreach ($rules as $rule) {
            $mform->addElement('header', "rule_{$rule->id}", 'Rule ' . ($rule->seq + 1));
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

        // Extract some object info.
        $id = isset($obj) ? "rule_{$obj->id}_" : 'rule_new_';
        $data = isset($obj) ? unserialize($obj->data) : null;

        // Where are we?
        //  - If we are new, default is '' otherwise the default is the object's value
        //    and we take the parameter as primary.
        $defaultrule = isset($obj) ? $obj->rule : '';
        $rule = optional_param($id . 'rule', $defaultrule, PARAM_ALPHANUMEXT);

        $defaulttarget = isset($obj) ? $obj->target : '';
        $target = optional_param($id . 'target', $defaulttarget, PARAM_ALPHANUMEXT);

        $defaultdatatype = isset($obj) ? $obj->datatype : '';
        $datatype = optional_param($id . 'datatype', $defaultdatatype, PARAM_ALPHANUMEXT);

        $defaultactivity = isset($data->activity) ? $data->activity : '';
        $activity = optional_param($id . 'activity', $defaultactivity, PARAM_ALPHANUMEXT);

        // Add a selection box for the rule.
        $validrules = \tool_cat\external::get_rules();
        $mform->addElement('select', $id . 'rule', 'Rule', array_merge(array(
            0 => 'Select a rule'
        ), $validrules), array(
            'class' => 'rule-select'
        ));

        // Do we have a rule?
        if (empty($rule)) {
            return;
        }

        // We have a rule!
        $validtargets = \tool_cat\external::get_targets($rule);
        $mform->addElement('select', $id . 'target', 'Target', array_merge(array(
            0 => 'Select a target'
        ), $validtargets), array(
            'class' => 'target-select'
        ));
        $mform->addElement('text', $id . 'targetid', 'Target Identifier');
        $mform->setType($id . 'targetid', PARAM_TEXT);

        // Do we have a target?
        if (empty($target)) {
            return;
        }

        // We have a target!
        $validdatatypes = \tool_cat\external::get_datatypes($target);
        $mform->addElement('select', $id . 'datatype', 'Data type', array_merge(array(
            0 => 'Select a data type'
        ), $validdatatypes), array(
            'class' => 'datatype-select'
        ));

        // Do we have a datatype?
        if (empty($datatype)) {
            return;
        }

        // Set defaults.
        if (isset($obj)) {
            $mform->setDefault($id . 'rule', $obj->rule);
            $mform->setDefault($id . 'target', $obj->target);
            $mform->setDefault($id . 'targetid', $obj->targetid);
            $mform->setDefault($id . 'datatype', $obj->datatype);
        }

        // We might need extra information.
        switch ($datatype) {
            case 'activity':
                $validactivities = \tool_cat\external::get_activities();
                $mform->addElement('select', $id . 'activity', 'Activity', array_merge(array(
                    0 => 'Select an activity'
                ), $validactivities), array(
                    'class' => 'activity-select'
                ));

                // Do we have an activity submitted?
                if (empty($activity)) {
                    return;
                }

                $mform->setDefault($id . 'activity', $activity);

                // Add fields.
                $validfields = \tool_cat\external::get_activity_fields($activity);
                foreach ($validfields as $name => $type) {
                    $mform->addElement('text', $id . $name, ucwords($name));
                    $mform->setType($id . $name, $type);

                    if (isset($obj) && isset($data->$name)) {
                        $mform->setDefault($id . $name, $data->$name);
                    }
                }
            break;

            case 'block':
                $validblocks = \tool_cat\external::get_blocks();
                $mform->addElement('select', $id . 'block', 'Block', array_merge(array(
                    0 => 'Select a block'
                ), $validblocks));

                /* TODO - move this to target identifier.
                $mform->addElement('select', $id . 'blockpos', 'Block Position', array(
                    \BLOCK_POS_LEFT => 'Left',
                    \BLOCK_POS_RIGHT => 'Right'
                ));
                 */

                if (isset($obj)) {
                    $mform->setDefault($id . 'block', isset($data->block) ? $data->block : '');
                }
            break;

            // For now - until we make the form dynamic.
            case 'standard':
                $mform->addElement('text', $id . 'title', 'Section title');
                $mform->setType($id . 'title', PARAM_TEXT);

                $mform->addElement('textarea', $id . 'summary', 'Section summary');
                $mform->setType($id . 'summary', PARAM_TEXT);

                if (isset($obj)) {
                    $mform->setDefault($id . 'title', isset($data->title) ? $data->title : '');
                    $mform->setDefault($id . 'summary', isset($data->summary) ? $data->summary : '');
                }
            break;

            case 'text':
                $mform->addElement('textarea', $id . 'text', 'Data');
                $mform->setType($id . 'text', PARAM_TEXT);

                if (isset($obj) && isset($data->text)) {
                    $mform->setDefault($id . 'text', $data->text);
                }
            break;

            default:
                // All done!
            break;
        }
    }

    /**
     * Returns rule data.
     *
     * @param array $data Form data.
     */
    public function get_rules($data) {
        static $rulefields = array(
            'categoryid',
            'rule',
            'target',
            'targetid',
            'datatype'
        );

        $rules = array();
        foreach ((array)$data as $k => $v) {
            if (substr_count($k, '_') != 2) {
                continue;
            }

            // Break up the key.
            list($rule, $id, $name) = explode('_', $k);
            if (!isset($rules[$id])) {
                $rules[$id] = new \stdClass();
                $rules[$id]->categoryid = $data->categoryid;
                $rules[$id]->data = new \stdClass();

                if ($id != 'new') {
                    $rules[$id]->id = $id;
                }
            }

            // Is this a standard field?
            if (in_array($name, $rulefields)) {
                // Yep, just add it.
                $rules[$id]->$name = $v;
            } else {
                // Nope? Data field then.
                $rules[$id]->data->$name = $v;
            }
        }

        // Check 'new' is complete.
        if (empty($rules['new']->rule) || empty($rules['new']->target)) {
            unset($rules['new']);
        }

        return $rules;
    }
}
