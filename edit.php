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
 * Version details
 *
 * @package    block_html
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/message/classes/form/edit.php');

global $DB;

$PAGE->set_url(new moodle_url('/local/message/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Edit');

//We want to display our form.
$mform = new edit();



if ($mform->is_cancelled()) {
    //go back to manage page
    redirect($CFG->wwwroot . '/local/message/manage.php', 'You cancelled the message form');


} else if ($fromform = $mform->get_data()) {
    //Insert the data into our database table.

    $recordtoinsert = new stdClass();
    $recordtoinsert->messagetext = $fromform->messagetext;
    $recordtoinsert->messagetype = $fromform->messagetype;

    $DB->insert_record('local_message', $recordtoinsert);

    //go back to manage.php page
    redirect($CFG->wwwroot . '/local/message/manage.php', 'You created a message with title' . $fromform->messagetext);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
