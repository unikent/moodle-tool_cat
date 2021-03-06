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
 * Output rendering of category admin tool.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Rendering methods for the catdatatype_template pages.
 */
class catdatatype_text_renderer extends plugin_renderer_base
{
    /**
     * Render a mustache template.
     *
     * @param  string   $text     The text to render.
     * @param  stdClass $context  Mustache variables.
     * @return string             The rendered text.
     */
    public function render_mustache_string($text, $context) {
        $mustache = $this->get_mustache();
        $template = $mustache->loadLambda($text);
        return $template->render($context);
    }
}
