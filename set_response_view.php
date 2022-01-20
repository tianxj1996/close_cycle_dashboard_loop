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
 * Set response view
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once(__DIR__ . '/locallib.php');
require_once('classes/set_response_form.php');

global $DB, $OUTPUT, $PAGE;

$courseid = required_param('courseid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$moduleid = required_param('moduleid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('wrongCourse', 'block_closed_loop_support', $courseid);
}

$form_parameters = array('courseid' => $courseid,
                         'sectionid' => $sectionid, 
                         'moduleid' => $moduleid);

require_login($course);

$PAGE->set_url('/blocks/closed_loop_support/set_response_view.php', $form_parameters);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Set closed loop response');

$reloadurl = new moodle_url('/blocks/closed_loop_support/set_response_view.php', $form_parameters);
$setresponse_form = new setresponse_form($courseid, $sectionid, $moduleid, $reloadurl);   
$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

if($setresponse_form->is_cancelled()) {
    redirect($courseurl);
} 
else if ($fromform = $setresponse_form->get_data()) {
    block_closed_loop_support_set_response_config($courseid, $moduleid, $fromform);
    redirect($courseurl);
} 
else {
    echo $OUTPUT->header();
    $resData = block_closed_loop_support_get_response_config($courseid, $moduleid);
    $setresponse_form->set_data($resData);
    $setresponse_form->display();
    echo $OUTPUT->footer();
}