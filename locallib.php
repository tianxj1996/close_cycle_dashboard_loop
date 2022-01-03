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

/**
 * Check if new requests exists for teacher in respect to teacher last request check
 * and return them
 * 
 * @return boolean Description
 *
*/

defined('MOODLE_INTERNAL') || die();


function block_closed_loop_support_get_new_requests_teacher_ids(int $userid, int $courseid = -1){
    global $DB;
    
    $tableTimestamp = 'block_closed_loop_teacher';
    $conditions = array('userid' => $userid, 'courseid' => $courseid);
    $counter = 0;
    if($courseid === -1){
        
        return $DB->get_records($tableTimestamp, ['userid' => $userid]);
    }
    else{
        return $DB->get_records($tableTimestamp, $conditions);
    }
}


function block_closed_loop_support_get_new_requests_teacher(int $userid, int $courseid = -1){
    global $DB, $CFG;
    
    $tableTimestamp = 'block_closed_loop_teacher';
    $conditions = array('userid' => $userid, 'courseid' => $courseid);
    $counter = 0;
    if($courseid === -1){
        
        $counter = count($DB->get_records($tableTimestamp, ['userid' => $userid]));
    }
    else{
        $counter = count($DB->get_records($tableTimestamp, $conditions));
    }
    if($counter > 1){
        $text = get_string('newRequests', 'block_closed_loop_support', $counter);
        $btnClass = "btn-warning";
    }
    else if ($counter === 1){
        $text = get_string('newRequest', 'block_closed_loop_support');
        $btnClass = "btn-warning";
    }
    else{
        $text = get_string('noRequest', 'block_closed_loop_support');
        $btnClass = "btn-info";
    }
    $url = new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", 
                    array('courseid'=> $courseid));
    
    return ['InfoText' => $text,
        'BtnClass' => $btnClass,
        'Url' => $url->out()];
}



function block_closed_loop_support_set_requests_viewed(int $userid, int $courseid = -1){
    global $DB;
    $tableTeacher = 'block_closed_loop_teacher';
    $conditions = array('userid' => $userid, 'courseid' => $courseid);
    
    
    if($courseid === -1){
        
        $DB->delete_records($tableTeacher, ['userid' => $userid]);
    }
    else{
        $DB->delete_records($tableTeacher, $conditions);
    }
}


function block_closed_loop_support_write_request(int $userid, int $courseid, int $cmid){
    global $DB;
    $table = 'block_closed_loop_support';
    $tableTeacher = 'block_closed_loop_teacher';
    $conditions = array('courseid' => $courseid, 'moduleid' => $cmid, 'userid' => $userid);
    $time = new DateTime("now", core_date::get_user_timezone_object());
    $timeStamp = $time->getTimestamp();

    if (!$DB->record_exists($table, $conditions)) {
        $counter = 1;
    } else {
        $counter = $DB->get_field($table, 'counter', $conditions) + 1;
    }

    $dataobject = array(
        'userid' => $userid,
        'courseid' => $courseid,
        'moduleid' => $cmid,
        'counter' => $counter,
        'timestamp' => $timeStamp
    );

    $requestid = $DB->insert_record($table, $dataobject);

    //Update unread for course-teacher
    $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $teachers = get_role_users($role->id, $context);
    $data = [];
    foreach ($teachers as $teacher) {
        $data[] = ['courseid' => $courseid, 'requestid' => $requestid, 'userid' => $teacher->id];
    }

    $DB->insert_records($tableTeacher, $data);
}