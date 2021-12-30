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
 * Point of View external lib
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@gmx.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

class block_closed_loop_support_external_data extends external_api {
    
    /**
    * Parameters definition for update_db (user not required here)
    */
    public static function write_requests_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, 'id of course module', VALUE_REQUIRED)
            )
        );
    }
    
    /**
    * Returns a log message
    *
    * @return external_description
    */
    public static function write_requests_returns() {
        return new external_value(PARAM_TEXT, 'Test');
    }
    
    
    public static function write_requests(int $courseid, int $cmid){
        global $USER;
        require_once(__DIR__ . '/locallib.php');
        return block_closed_loop_support_write_request($USER->id, $courseid, $cmid);
    }
    
    
    /**
    * Parameters definition for read_requests
    */
    public static function read_requests_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_DEFAULT, -1)
            )
        );
    }
    
    
    /**
    * read_requests
    */
    public static function read_requests(int $courseid) {
        global $USER;
        require_once(__DIR__ . '/locallib.php');
        return block_closed_loop_support_get_new_requests_teacher($USER->id, $courseid);
    }
    
    
    /**
    * Return values of read_requests
    *
    * @return external_multiple_structure (requests)
    */
    public static function read_requests_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'ID of db entry'),
                    'courseid' => new external_value(PARAM_INT, 'Id of course'),
                    'userid' => new external_value(PARAM_INT, 'Id of student who requested'),
                    'moduleid' => new external_value(PARAM_INT, 'Id of requested module'),
                    'counter' => new external_value(PARAM_INT, 'Number of request.'),
                    'timestamp' => new external_value(PARAM_INT, 'Timestamp of request.'),
                ]
            )
        );
    }
    
}