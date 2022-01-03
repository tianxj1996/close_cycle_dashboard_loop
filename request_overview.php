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
 * @author     Rene Hilgemann <rene.hilgemann@gmx.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');
global $CFG, $PAGE, $OUTPUT, $DB, $USER;
require "$CFG->libdir/tablelib.php";
require "classes/request_table.php";
require_login();

$courseid = optional_param('courseid', -1, PARAM_INT);
if($courseid !== -1){
    $context = context_course::instance($courseid);
    $course = get_course($courseid);
    $PAGE->set_course($course);
}
else
{
    $context = context_system::instance();
}

$PAGE->set_context($context);

$parameters = array (
        'courseid'   => $courseid
);

$PAGE->set_url(new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", $parameters));
require_capability('block/closed_loop_support:access_requests', $context);
$download = optional_param('download', '', PARAM_ALPHA);

//TODO: german translation!
$table = new request_table('uniqueid');
$table->courseid = $courseid;
$table->is_downloading($download, 'Requests_overview', 'Overview about requests');

if (!$table->is_downloading()) {
    $PAGE->set_title('Requests overview');
    $PAGE->set_heading('Overview about requests');
    $PAGE->navbar->add('Overview about requests', 
            new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", $parameters));
    $PAGE->set_pagelayout('report');
    echo $OUTPUT->header();
}

$conditions = array('userid' => $USER->id, 'courseid' => $courseid);
$unreadRequests = block_closed_loop_support_get_new_requests_teacher_ids($USER->id, $courseid);
foreach($unreadRequests as $unread){
    array_push($table->unreadRequests, $unread->requestid);
}

        
// Work out the sql for the table.
$table->set_sql('{block_closed_loop_support}.id, {block_closed_loop_support}.courseid,'
        . '{block_closed_loop_support}.userid, {block_closed_loop_support}.moduleid,'
        . '{block_closed_loop_support}.counter, {block_closed_loop_support}.timestamp,'
        . '{user}.firstname, {user}.lastname, {user}.username'
        , "{block_closed_loop_support}, {user}", "{user}.id = {block_closed_loop_support}.userid");

$table->define_baseurl("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", $parameters);

$table->out(20, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}

// Trigger event, course requests viewed for user in course.
$eventparams = array('courseid' => $courseid, 'userid' => $USER->id, 'contextid' => $context->id);
$event = \block_closed_loop_support\event\course_requests_viewed::create($eventparams);
$event->trigger();