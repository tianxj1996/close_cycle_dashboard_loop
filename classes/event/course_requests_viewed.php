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
 * event class course_requests_viewd
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_closed_loop_support\event;
defined('MOODLE_INTERNAL') || die();


class course_requests_viewed extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }


    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        if($this->courseid < 0){
            return "The user with id '$this->userid' viewed the requests for all courses he/she is responsible for'.";
        }
        else{
            return "The user with id '$this->userid' viewed the requests for the course with id '$this->courseid'.";
        }
    }

    
    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcourserequestsviewed', 'block_closed_loop_support');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url|null
     */
    public function get_url() {
        global $CFG;
        return new \moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", array('id' => $this->courseid));
    }

}