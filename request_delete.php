<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');
global $CFG, $OUTPUT, $DB, $USER;

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
$userid = $USER->id;
if (has_capability('block/closed_loop_support:access_requests', $context)) {
    $userid = 0;
}
if ($userid && $record->userid != $userid) {
    exit('you can not delete this request');
}
$DB->delete_records('block_closed_loop_support', ['id' => $requestid]);
header("Location: /blocks/closed_loop_support/request_overview.php?courseid=" . $record->courseid);