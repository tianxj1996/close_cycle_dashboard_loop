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
function block_closed_loop_support_get_new_requests_teacher($userid = -1, $courseid = -1){
    global $DB;
    
    $requests = [];
    
    if($userid < 0){
        return $requests;
    }
    
    $tableTimestamp = 'block_closed_loop_teacher_ti';
    $conditions = array('userid' => $userid, 'courseid' => $courseid);
    $coursesCap = get_user_capability_course('block/closed_loop_support:access_requests', $userid);
    
    if(!$coursesCap){
        return $requests;
    }
    $arrcourseCapIds = [];
    foreach ($coursesCap as $val){
        $arrcourseCapIds[] = $val->id;
    }

    $timestampRecordExists = $DB->record_exists($tableTimestamp, $conditions);
    list($insql, $inparams) = $DB->get_in_or_equal($arrcourseCapIds);
    if($timestampRecordExists === false && $courseid < 0){
        $sql = "SELECT * FROM {block_closed_loop_support} WHERE courseid $insql";
    }
    else if($timestampRecordExists)
    {
        $timestampData = $DB->get_record($tableTimestamp, $conditions);
        if($courseid < 0){
            $sql = "SELECT * FROM {block_closed_loop_support} WHERE courseid $insql AND timestamp > :timestamp";
            $inparams [] = ['timestamp' => $timestampData->timestamp];
        }
        else{
            $sql = "SELECT * FROM {block_closed_loop_support} WHERE courseid $insql AND timestamp > :timestamp AND courseid = :courseid";
            $inparams [] = ['timestamp' => $timestampData->timestamp];
            $inparams [] = ['courseid' => $courseid];
        }
    }
    $requests = $DB->get_records_sql($sql, $inparams);
    return $requests;
}