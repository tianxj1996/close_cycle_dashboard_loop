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
 * Overview of requests
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');
global $CFG, $PAGE, $OUTPUT, $DB, $USER;
require "$CFG->libdir/tablelib.php";
require "classes/request_table.php";
require_login();

$requestid = optional_param('requestid', -1, PARAM_INT);
if($requestid == -1){
    exit('params error');
}
$record = $DB->get_record('block_closed_loop_support', ['id' => $requestid]);
if (!$record) {
    exit('request not exist');
}
$courseid = $record->courseid;
$context = context_course::instance($courseid);
$course = get_course($courseid);
$PAGE->set_course($course);

$parameters = array (
    'requestid'   => $requestid
);
$PAGE->set_url(new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_detail.php", $parameters));
$PAGE->set_title('Request detail');

$userid = $USER->id;
if (has_capability('block/closed_loop_support:access_requests', $context)) {
    if ($record->userid != $userid) {
        $tableTeacher = 'block_closed_loop_teacher';
        $DB->delete_records($tableTeacher, ['requestid' => $requestid]);
    }
    $userid = 0;
}
if ($userid && $record->userid != $userid) {
    throw new required_capability_exception($context, 'block/closed_loop_support:access_requests', 'nopermissions', '');
}

echo $OUTPUT->header();

$sqlWhere = "{user}.id = {block_closed_loop_support}.userid AND "
    . "{block_closed_loop_support}.pid = $requestid";
$sql = "SELECT {block_closed_loop_support}.id, {block_closed_loop_support}.courseid,{block_closed_loop_support}.userid, {block_closed_loop_support}.moduleid, {block_closed_loop_support}.counter, {block_closed_loop_support}.timestamp, {block_closed_loop_support}.explanationtext, {block_closed_loop_support}.explanationsend, {user}.firstname, {user}.lastname, {user}.username FROM {block_closed_loop_support}, {user} WHERE $sqlWhere ORDER BY id ASC";
$list = $DB->get_records_sql($sql, null, 0, 30);

$explanation = unserialize(base64_decode($record->explanationtext));
$user = $DB->get_record('user', ['id' => $record->userid]);
$cms = get_fast_modinfo($courseid);
$cm = $cms->get_cm($record->moduleid);
$modulename = $cm->get_formatted_name();
$coursename = get_course($courseid)->fullname;

$output = get_string('expHeader', 'block_closed_loop_support') . "<br>";
$output .= $explanation;
foreach ($list as $val) {
    $explanationtext = unserialize(base64_decode($val->explanationtext));
    $output .= "<div><span>" . $val->username . " reply: </span><br><span>{$explanationtext}</span></div>";
}
$output .= "<hr>";
$output .= get_string('dataHeader', 'block_closed_loop_support') . "<br>";
$output .= get_string('user', 'block_closed_loop_support'). ": ". $user->firstname. " " . $user->lastname . "<br>";
$output .= get_string('module', 'block_closed_loop_support'). ": " . $modulename. "<br>";
$output .= get_string('course', 'block_closed_loop_support'). ": " .$coursename. "<br>";
echo $output;

require 'classes/reply_explanation_form.php';
$mform = new reply_explanation_form($requestid, 'request_reply.php');
$mform->display();

$tableReply = 'block_closed_loop_reply';
$DB->delete_records($tableReply, ['requestid' => $requestid, 'userid' => $userid]);

echo $OUTPUT->footer();

// Trigger event, course requests viewed for user in course.
$eventparams = array('courseid' => $courseid, 'userid' => $USER->id, 'contextid' => $context->id);
$event = \block_closed_loop_support\event\course_requests_viewed::create($eventparams);
$event->trigger();