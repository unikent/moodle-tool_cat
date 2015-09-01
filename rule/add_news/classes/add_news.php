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
 * Category admin tool rules.
 *
 * @package    catrule_add_news
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace catrule_add_news;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Category admin tool news rule.
 *
 * @package    catrule_add_news
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_news extends \tool_cat\rule
{
    /**
     * Return a list of targets this rule supports.
     *
     * @return array An array of valid targets.
     */
    public function get_supported_targets() {
        return array(
            'course'
        );
    }

    /**
     * Apply the rule.
     *
     * @param array $courses An array of courses to apply to rule to.
     * @return array An array of courses we applied ourselves to.
     */
    protected function _apply($courses) {
        foreach ($courses as $course) {
            $this->add_to_course($course);
        }

        return $courses;
    }

    /**
     * Append a news activity to the given course.
     *
     * @param  stdClass $course        The course to apply to.
     */
    public function add_to_course($course) {
        global $DB;

        $forum = forum_get_course_forum($course->id, 'news');

        $data = (object)$this->get_data();
        if (empty($data)) {
            return;
        }

        // Set some extra vars.
        if (isset($data->intro)) {
            $DB->set_field('forum', 'intro', $data->intro, array(
                'id' => $forum->id
            ));
        }

        if (isset($data->showdescription)) {
            $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id);
            if ($cm) {
                $DB->set_field('course_modules', 'showdescription', $data->showdescription, array(
                    'id' => $cm->id
                ));
            }
        }
    }
}
