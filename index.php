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
 * Category admin tool index.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('categoryadmintool');

$PAGE->set_context(\context_system::instance());
$PAGE->set_url('/admin/tool/cat/index.php');

$form = new \tool_cat\form\category_rules();

if (($data = $form->get_data())) {
    $rules = $form->get_rules($data);
    foreach ($rules as $rule) {
        $rule->data = serialize($rule->data);
        $rule->config = json_encode($rule->config);

        if (isset($rule->id)) {
            $DB->update_record('tool_cat_rules', $rule);
        } else {
            $seq = $DB->get_field('tool_cat_rules', 'MAX(seq)', array(
                'categoryid' => $rule->categoryid
            ));

            $rule->seq = isset($seq) ? $seq + 1 : 0;

            $DB->insert_record('tool_cat_rules', $rule);
        }
    }

    redirect(new \moodle_url($PAGE->url, array('categoryid' => $data->categoryid)));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_cat'));

echo $OUTPUT->box("Add a rule for courses within a category. This will be applied to current and future courses.");

$form->display();

echo $OUTPUT->footer();
