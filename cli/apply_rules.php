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
 * Category admin CLI tool.
 *
 * @package    tool_cat
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params(
    array(
        'category' => 0,
    )
);

if (empty($options['category'])) {
    print_error("You must specify a category with --category.");
    exit(0);
}

$category = new \tool_cat\category($options['category']);
$category->apply_rules();