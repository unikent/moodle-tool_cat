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
        $mform =& $this->_form;

        // Select a category.
        $categories = $this->get_categories();
        $mform->addElement('select', 'category', 'Category', $categories);


        $this->add_action_buttons(true);
    }

    /**
     * Return all categories we are enrolled in.
     */
    public function get_categories() {
        global $DB;

        $contextpreload = \context_helper::get_preload_record_columns_sql('x');

        $categories = array();
        $rs = $DB->get_recordset_sql("SELECT cc.id, cc.name, $contextpreload
                                        FROM {course_categories} cc
                                        JOIN {context} x ON (cc.id=x.instanceid AND x.contextlevel=".CONTEXT_COURSECAT.")");

        // Check capability for each category in turn.
        foreach ($rs as $category) {
            \context_helper::preload_from_record($category);
            $context = \context_coursecat::instance($category->id);
            if (has_capability('tool/cat:manage', $context)) {
                $categories[$category->id] = $category->name;
            }
        }

        $rs->close();

        return $categories;
    }
}
