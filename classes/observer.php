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
 * event-oberserver class 
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_closed_loop_support_observer{
    
     /**
     * Process course_requests_viewed event
     * @param \block_closed_loop_support\event\course_requests_viewed $event 
     */
    public static function requests_viewed(\block_closed_loop_support\event\course_requests_viewed $event){
        require_once(__DIR__ . '/../locallib.php');
        
        //block_closed_loop_support_set_requests_viewed($event->userid, $event->courseid);
    }
    
    /**
     * Process module_request_generated event (actually only placeholder cause it is required for definition of new event)
     * @param \block_closed_loop_support\event\module_request_generated $event 
     */
    public static function request_generated(\block_closed_loop_support\event\module_request_generated $event){
        return;
    }
    
    /**
     * Process request_explanation_viewed event (actually only placeholder cause it is required for definition of new event)
     * @param \block_closed_loop_support\event\request_explanation_viewed $event 
     */
    public static function explanation_viewed(\block_closed_loop_support\event\request_explanation_viewed $event){
        return;
    }
    
    /**
     * Process request_explanation_submitted event (actually only placeholder cause it is required for definition of new event)
     * @param \block_closed_loop_support\event\request_explanation_submitted $event 
     */
    public static function explanation_submitted(\block_closed_loop_support\event\request_explanation_submitted $event){
        return;
    }
    
        /**
     * Process module_response_updated event (actually only placeholder cause it is required for definition of new event)
     * @param \block_closed_loop_support\event\module_response_updated $event 
     */
    public static function response_updated(\block_closed_loop_support\event\module_response_updated $event){
        return;
    }
    
     /**
     * Process course_module_created event
     * @param \core\event\course_module_created $event 
     */
    public static function module_added(\core\event\course_module_created $event){
        require_once(__DIR__ . '/../locallib.php');
        block_closed_loop_support_create_response($event->courseid, [$event->objectid]);
    }
    
     /**
     * Process course_module_deleted event
     * @param \core\event\course_module_deleted $event 
     */
    public static function module_deleted(\core\event\course_module_deleted $event){
        require_once(__DIR__ . '/../locallib.php');
        block_closed_loop_support_delete_response($event->courseid, [$event->objectid]);
    }
    
         /**
     * Process course_module_deleted event
     * @param \core\event\course_module_deleted $event 
     */
    public static function course_deleted(\core\event\course_deleted $event){
        require_once(__DIR__ . '/../locallib.php');
        block_closed_loop_support_delete_response($event->courseid);
    }
    

}