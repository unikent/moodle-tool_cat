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
 * Category admin tool datatypes.
 *
 * @package    catdatatype_text
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catdatatype_text;

defined('MOODLE_INTERNAL') || die();

/**
 * Category admin tool text data type.
 * Optionally rendered by the Mustache library.
 *
 * @package    catdatatype_text
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text extends \tool_cat\datatype
{
    /**
     * Render a mustache template.
     *
     * @param  string   $text     The text to render.
     * @param  stdClass $context  Mustache variables.
     * @return string             The rendered text.
     */
    protected function render_template($text, $context) {
        global $PAGE;

        $renderer = $PAGE->get_renderer('catdatatype_text');
        return $renderer->render_mustache_string($text, $context);
    }

    /**
     * Return the rendered text.
     *
     * @return string The data.
     */
    public function get_data() {
        $data = parent::get_data();

        $text = $data->text;

        // Check we aren't a template.
        if (isset($data->template) && $data->template) {
            $context = $this->get_context();
            if (empty($context)) {
                debugging("Cannot render a template string with a blank context! Make sure you call set_context first.");
                $context = new \stdClass();
            }

            // Render as a template.
            $text = $this->render_template($text, $context);
        }

        return $text;
    }
}
