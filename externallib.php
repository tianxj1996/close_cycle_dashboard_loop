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
 * Class for external_api
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

class block_closed_loop_support_external_data extends external_api {
    
    /**
    * Return html for modal dialog
    *
    * @return external_multiple_structure
    */
    public static function get_explanation_returns() {
        return new external_value(PARAM_RAW, 'Explanation html');
    }
    
    /**
    * Parameters definition for get_modal_body
    *
    * @return external_value
    */
    public static function get_explanation_parameters() {
        return new external_function_parameters(
            array(
                'requestid' => new external_value(PARAM_INT, 'id of original request of explanation', VALUE_REQUIRED)
            )
        );
    }
    
    /**
    * get_explanation
    */
    public static function get_explanation(int $requestid) {
        require_once(__DIR__ . '/locallib.php');
        return block_closed_loop_support_get_explanation($requestid);
    }

    /**
    * Parameters definition for write_explanation
    */
    public static function write_explanation_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, 'id of course module', VALUE_REQUIRED),
                'counter' => new external_value(PARAM_INT, 'counter of request', VALUE_REQUIRED),
                'explanation' => new external_value(PARAM_RAW, 'text of explanation', VALUE_REQUIRED)
            )
        );
    }
    
    /**
    * Returns a log message
    *
    * @return write_explanation
    */
    public static function write_explanation_returns() {
        return new external_value(PARAM_TEXT, 'Placeholder');
    }
    
    
    public static function write_explanation(int $courseid, int $cmid, int $counter, string $explanation){
        global $USER;
        require_once(__DIR__ . '/locallib.php');
        return block_closed_loop_support_write_explanation($USER->id, $courseid, $cmid, $counter, $explanation);
    }
    
    
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
        return new external_value(PARAM_INT, 'Counter number');
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
        return 
            new external_single_structure(
                [
                    'InfoText' => new external_value(PARAM_RAW, 'Output string.'),
                    'BtnClass' => new external_value(PARAM_RAW, 'CSS class of link'),
                    'Url' => new external_value(PARAM_RAW, 'URL for link')
                ]
            );
    }
    
    /**
    * Return base html for modal dialog
    *
    * @return external_multiple_structure
    */
    public static function get_modal_body_returns() {
        return 
            new external_single_structure(
                [
                    'modalbody' => new external_value(PARAM_RAW, 'Html for body'),
                    'size' => new external_value(PARAM_BOOL, 'Dialog size (true == large)'),
                    'explanationForwarding' => new external_value(PARAM_INT, 'Explanation forwarding is mandatory, optional or off')
                ]
            );
    }
    
    /**
    * Parameters definition for get_modal_body
    *
    * @return external_value
    */
    public static function get_modal_body_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'moduleid' => new external_value(PARAM_INT, 'id of course module', VALUE_REQUIRED)
            )
        );
    }
    
    /**
    * get_response_content
    */
    public static function get_modal_body(int $courseid, int $moduleid) {
        require_once(__DIR__ . '/locallib.php');
        return block_closed_loop_support_get_modal_body($courseid, $moduleid);
    }
    
    /**
    * Return title and content for get_response_content
    *
    * @return external_multiple_structure
    */
    public static function get_responselist_html_returns() {
        return new external_value(PARAM_RAW, 'Response list');
    }
    
    /**
    * Parameters definition for get_response_content
    *
    * @return external_value
    */
    public static function get_responselist_html_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
            )
        );
    }
    
    /**
    * get_response_content
    */
    public static function get_responselist_html(int $courseid) {
        require_once(__DIR__ . '/locallib.php');
        return block_closed_loop_support_get_responselist_html($courseid);
    }
}