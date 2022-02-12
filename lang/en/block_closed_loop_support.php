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
 * Strings for component 'block_closed_loop_support', language 'en'
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Closed loop support';
$string['noRequest'] = 'No new feedback-requests';
$string['newRequests'] = 'There are {$a} new requests';
$string['newRequest'] = 'There is one new request';
$string['forAll'] = 'for any course';
$string['forCourse'] = 'for this course';
$string['blockAccessDenied'] = 'Access denied for request overview';
$string['eventcourserequestsviewed'] = 'Requests for course viewed';
$string['eventrequestexplanationviewed'] = 'Explanation for help request viewed';
$string['eventrequestexplanationsubmitted'] = 'Explanation for help request submitted';
$string['eventresponseupdated'] = 'Response generated or updated';
$string['eventmoduleRequstGenerated'] = 'Request for course-module generated';
$string['setResponse'] = 'Set response content';
$string['setSectionModule'] = 'Course section and module';
$string['defResModule'] = '<h5>Define responses for modules</h5>';
$string['defNoRespAddable'] = '<br>On course-page responses can be added here';
$string['defMissingCapabilitys'] = '<br><b>You have not required capabilitys!</b>';
$string['overviewHeadingCourse'] = 'Overview about requests in course';
$string['overviewHeadingAll'] = 'Overview about requests for all responsible courses';
$string['wrongCourse'] = 'Course with id {$a} not found';
$string['responseActive'] = 'Set response active';
$string['responseActive_help'] = 'General setting if this response is active and related response button visible on course-page';
$string['responseContent'] = 'Editor for content definition';
$string['responseContent_help'] = 'Editor for defining of content of the response (e.g. text, pictures, links to ressources or external websites)';
$string['responseSetSize'] = 'Define size of response dialog';
$string['responseSetSize_help'] = 'Define the default size of the output dialog. Size only visible in the real dialog, not here.';
$string['explanationForwarding'] = 'Explanation for request behaviour';
$string['explanationForwarding_help'] = 'Define if an explanation for a request is required. Options:<br>No = No explanation possible<br>'
        . 'Optional = Is possible but not required to view response content<br>Mandatory = Required to view response content';
$string['explainHeading'] = '{$a} explanation for help request.';
$string['explainHeading2'] = 'Further support information are visible after submit of explanation!';
$string['explainFormGeneral'] = 'Explain';
$string['explainFormGeneral_help'] = 'Explanation will be forwarded to course responsible';
$string['missingExplanation'] = 'No empty explanation aloud for submit';
$string['responseTitle'] = 'Help response';
$string['expThanks'] = 'Thank you! Your explanation has been forwarded.';
$string['overviewExplainYes'] = 'User provided a explanation for request';
$string['overviewExplainNo'] = 'User provided no explanation for request';
$string['explainShowTitle'] = 'User explanation for request';
$string['module'] = 'Module';
$string['course'] = 'Course';
$string['user'] = 'User';
$string['expHeader'] = '<h6>Explanation text</h6>';
$string['dataHeader'] = '<h6>Data</h6>';

$string['closed_loop_support:access_requests'] = 'Overview about closed loop requests';
$string['closed_loop_support:generate_requests'] = 'Generate a closed loop request for a course-module';
$string['closed_loop_support:add_response'] = 'Add response to course-module for closed loop requests';
$string['closed_loop_support:addinstance'] = 'Add a Closed loop support  instance';
$string['closed_loop_support:myaddinstance'] = 'Add a Closed loop support block instance on dashboard';