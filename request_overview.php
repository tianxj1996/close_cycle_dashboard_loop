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

$courseid = optional_param('courseid', -1, PARAM_INT);
$newRequest = optional_param('newrequest', -1, PARAM_INT);
$newReply = optional_param('newreply', -1, PARAM_INT);
if($courseid !== -1){
    $context = context_course::instance($courseid);
    $course = get_course($courseid);
    $PAGE->set_course($course);
}
else
{
    //In this case the dialog has to be started from dashboard and the user
    //needs block/closed_loop_support:myaddinstance for its own usercontext 
    $context = context_user::instance($USER->id);
}

$PAGE->set_context($context);

$parameters = array (
        'courseid'   => $courseid
);

$url = new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", $parameters);
$PAGE->set_url($url);
$userid = $USER->id;
if($courseid !== -1){
    if (has_capability('block/closed_loop_support:access_requests', $context)) {
        $userid = 0;
    }
    //require_capability('block/closed_loop_support:access_requests', $context);
}
else{
    require_capability('block/closed_loop_support:myaddinstance', $context);
}

$ids = $DB->get_fieldset_select('block_closed_loop_support', 'id', 'explanationsend = 1');
$PAGE->requires->js_call_amd('block_closed_loop_support/script_closed_loop_load_explanation', 
       'init', [$ids]);

$download = optional_param('download', '', PARAM_ALPHA);

$table = new request_table('uniqueid');
$table->courseid = $courseid;
$headingText = get_string('overviewHeadingCourse', 'block_closed_loop_support');
$headingTextAll = get_string('overviewHeadingAll', 'block_closed_loop_support');
$table->is_downloading($download, 'Requests_overview', $headingText);

if (!$table->is_downloading()) {
    $PAGE->set_title('Requests overview');
    $PAGE->set_heading($courseid == -1 ? $headingTextAll : $headingText);
    $PAGE->navbar->add($headingText, 
            new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", $parameters));
    $PAGE->set_pagelayout('report');
    echo $OUTPUT->header();
}

$conditions = array('userid' => $USER->id, 'courseid' => $courseid);
$unreadRequests = block_closed_loop_support_get_new_requests_teacher_ids($USER->id, $courseid);
$unreadReplies = block_closed_loop_support_get_new_replies_ids($USER->id, $courseid, $userid);
foreach($unreadRequests as $unread){
    array_push($table->unreadRequests, $unread->requestid);
}
foreach($unreadReplies as $unread){
    array_push($table->unreadReplies, $unread->requestid);
}


if($courseid == -1){
    $sqlWhere = "{user}.id = {block_closed_loop_support}.userid AND {block_closed_loop_support}.pid = 0";
}
else{
    $sqlWhere = "{user}.id = {block_closed_loop_support}.userid AND {block_closed_loop_support}.pid = 0 AND "
            . "{block_closed_loop_support}.courseid = $courseid";
    if ($userid) {
        $sqlWhere .= " AND {block_closed_loop_support}.userid = $userid";
    }
}
if ($newRequest != -1) {
    if (empty($table->unreadRequests)) {
        $sqlWhere .= " AND {block_closed_loop_support}.id = -1";
    } else {
        $sqlWhere .= " AND {block_closed_loop_support}.id in (" . implode(',', $table->unreadRequests) . ")";
    }
}
if ($newReply != -1) {
    if (empty($table->unreadReplies)) {
        $sqlWhere .= " AND {block_closed_loop_support}.id = -1";
    } else {
        $sqlWhere .= " AND {block_closed_loop_support}.id in (" . implode(',', $table->unreadReplies) . ")";
    }
}

$table->set_sql('{block_closed_loop_support}.id, {block_closed_loop_support}.courseid,'
        . '{block_closed_loop_support}.userid, {block_closed_loop_support}.moduleid,'
        . '{block_closed_loop_support}.counter, {block_closed_loop_support}.timestamp,'
        . '{block_closed_loop_support}.explanationtext, {block_closed_loop_support}.explanationsend,'
        . '{user}.firstname, {user}.lastname, {user}.username'
        , "{block_closed_loop_support}, {user}", $sqlWhere);

$table->define_baseurl(new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", $parameters));
echo '<div><a href="' . $url . '" class="btn btn-primary mr-1">Show all</a><a href="' . $url . '&newrequest=1" class="btn btn-warning mr-1">Show new request</a><a href="' . $url . '&newreply=1" class="btn btn-danger">Show new reply</a></div>';
$table->out(20, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}

// Trigger event, course requests viewed for user in course.
$eventparams = array('courseid' => $courseid, 'userid' => $USER->id, 'contextid' => $context->id);
$event = \block_closed_loop_support\event\course_requests_viewed::create($eventparams);
$event->trigger();