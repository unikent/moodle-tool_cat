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

require_once($CFG->dirroot.'/lib/formslib.php');

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
        global $CFG, $PAGE;

        require_once($CFG->libdir. '/coursecatlib.php');

        $PAGE->requires->js_call_amd('tool_cat/form', 'init', array());

        $mform =& $this->_form;

        // Select a category.
        $mform->addElement('header', 'info', 'Category');
        $categories = \coursecat::make_categories_list('tool/cat:manage');
        $mform->addElement('select', 'category', 'Category', array_merge(array('0' => 'Please select a category'), $categories));

        $mform->registerNoSubmitButton('updaterules');
        $mform->addElement('submit', 'updaterules', 'Update Rules', array('class' => 'hidden'));

        $mform->addElement('header', 'rules', 'Rules');

        // Do we have a category? If so, populate existing rules.
        // TODO.

        // Print a blank rule-add row.
        // TODO.

        // Print an "add another rule" link.
        // TODO.

        $this->add_action_buttons(true);
    }
}
