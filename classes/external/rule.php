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

namespace tool_cat\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use external_api;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_function_parameters;

/**
 * Category admin tool's rules services.
 */
class rule extends external_api
{
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_rules_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Expose to AJAX.
     *
     * @return boolean
     */
    public static function get_rules_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Get a list of valid rules.
     *
     * @return array [string]
     * @throws \invalid_parameter_exception
     */
    public static function get_rules() {
        return array(
            'append_to',
            'delete',
            'empty_content',
            'prepend_to'
        );
    }

    /**
     * Returns description of get_rules() result value.
     *
     * @return external_description
     */
    public static function get_rules_returns() {
        return new external_multiple_structure(new external_value(PARAM_ALPHANUMEXT, 'A list of valid rules.'));
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_category_rules_parameters() {
        return new external_function_parameters(array(
            'category' => new external_value(
                PARAM_INT,
                'The category to get the rules for.',
                VALUE_REQUIRED
            )
        ));
    }

    /**
     * Expose to AJAX.
     *
     * @return boolean
     */
    public static function get_category_rules_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Return a list of existing rules for a given category.
     *
     * @param $category
     * @return array [string]
     * @throws \invalid_parameter_exception
     */
    public static function get_category_rules($category) {
        global $DB;

        $params = self::validate_parameters(self::get_category_rules_parameters(), array(
            'category' => $category
        ));

        return $DB->get_records('tool_cat_rules', array(
            'categoryid' => $params['category']
        ));
    }

    /**
     * Returns description of get_category_rules() result value.
     *
     * @return external_description
     */
    public static function get_category_rules_returns() {
        return new external_multiple_structure(new external_single_structure(array(
            new external_value(PARAM_INT,         'ID'),
            new external_value(PARAM_INT,         'Category'),
            new external_value(PARAM_INT,         'Order'),
            new external_value(PARAM_ALPHANUMEXT, 'Rule type'),
            new external_value(PARAM_ALPHANUMEXT, 'Target type'),
            new external_value(PARAM_RAW,         'Target identifier'),
            new external_value(PARAM_ALPHANUMEXT, 'Data type'),
            new external_value(PARAM_RAW,         'Data')
        )));
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_targets_parameters() {
        return new external_function_parameters(array(
            'rule' => new external_value(
                PARAM_ALPHANUMEXT,
                'The rule classname to get valid targets for.',
                VALUE_REQUIRED
            )
        ));
    }

    /**
     * Expose to AJAX.
     *
     * @return boolean
     */
    public static function get_targets_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns a list of valid targets for a given rule.
     *
     * @param $rule
     * @return array [string]
     * @throws \invalid_parameter_exception
     */
    public static function get_targets($rule) {
        $params = self::validate_parameters(self::get_category_rules_parameters(), array(
            'rule' => $rule
        ));

        $obj = \tool_cat\rule\base::create_rule($params['rule']);
        return $obj->get_supported_targets();
    }

    /**
     * Returns description of get_targets() result value.
     *
     * @return external_description
     */
    public static function get_targets_returns() {
        return new external_multiple_structure(new external_value(PARAM_ALPHANUMEXT, 'A list of valid targets for a given rule.'));
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_datatypes_parameters() {
        return new external_function_parameters(array(
            'target' => new external_value(
                PARAM_ALPHANUMEXT,
                'The target classname to get valid datatypes for.',
                VALUE_REQUIRED
            )
        ));
    }

    /**
     * Expose to AJAX.
     *
     * @return boolean
     */
    public static function get_datatypes_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Returns a list of valid data types for a given target.
     *
     * @param $target
     * @return array [string]
     * @throws \invalid_parameter_exception
     */
    public static function get_datatypes($target) {
        $params = self::validate_parameters(self::get_category_rules_parameters(), array(
            'target' => $target
        ));

        $obj = \tool_cat\target\base::create_target($params['target']);
        return $obj->get_supported_datatypes();
    }

    /**
     * Returns description of get_datatypes() result value.
     *
     * @return external_description
     */
    public static function get_datatypes_returns() {
        return new external_multiple_structure(new external_value(PARAM_ALPHANUMEXT, 'A list of valid data types for a given target.'));
    }
}