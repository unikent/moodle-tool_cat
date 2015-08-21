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

$services = array(
    'Category admin tool service' => array(
        'functions' => array (
            'tool_cat_get_rules',
            'tool_cat_get_category_rules',
            'tool_cat_get_targets',
            'tool_cat_get_datatypes',
            'tool_cat_get_activities'
        ),
        'requiredcapability' => 'tool/cat:manage',
        'restrictedusers' => 0,
        'enabled' => 1
    )
);

$functions = array(
    'tool_cat_get_rules' => array(
        'classname'   => 'tool_cat\external\rule',
        'methodname'  => 'get_rules',
        'description' => 'Get rules.',
        'type'        => 'read'
    ),
    'tool_cat_get_category_rules' => array(
        'classname'   => 'tool_cat\external\rule',
        'methodname'  => 'get_category_rules',
        'description' => 'Get rules for a given category.',
        'type'        => 'read'
    ),
    'tool_cat_get_targets' => array(
        'classname'   => 'tool_cat\external\rule',
        'methodname'  => 'get_targets',
        'description' => 'Get targets.',
        'type'        => 'read'
    ),
    'tool_cat_get_datatypes' => array(
        'classname'   => 'tool_cat\external\rule',
        'methodname'  => 'get_datatypes',
        'description' => 'Get datatypes.',
        'type'        => 'read'
    ),
    'tool_cat_get_activities' => array(
        'classname'   => 'tool_cat\external\rule',
        'methodname'  => 'get_activities',
        'description' => 'Get activities.',
        'type'        => 'read'
    )
);
