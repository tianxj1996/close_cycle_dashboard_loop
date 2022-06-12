<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');
global $CFG, $OUTPUT, $DB, $USER;

require_login();
$requestid = optional_param('requestid', -1, PARAM_INT);
//$explanation = optional_param('explanation_textarea', '', PARAM_HT);
$explanation = isset($_POST['explanation_textarea']) ? $_POST['explanation_textarea'] : '';
if (is_array($explanation)) {
    $explanation = $explanation['text'];
}
if($requestid == -1 || !$explanation){
    exit('params error');
}
$record = $DB->get_record('block_closed_loop_support', ['id' => $requestid]);
if (!$record) {
    exit('request not exist');
}

$insert = [
    'courseid' => $record->courseid,
    'userid' => $USER->id,
    'moduleid' => $record->moduleid,
    'counter' => 1,
    'timestamp' => time(),
    'explanationtext' => base64_encode(serialize($explanation)),
    'explanationsend' => 1,
    'pid' => $requestid,
];
$DB->insert_record('block_closed_loop_support', $insert);

$role = $DB->get_record('role', array('shortname' => 'editingteacher'));
$context = context_course::instance($record->courseid);
$teachers = get_role_users($role->id, $context);
foreach ($teachers as $teacher) {
    if ($teacher->id != $record->userid) {
        $insert = [
            'courseid' => $record->courseid,
            'requestid' => $requestid,
            'userid' => $teacher->id,
        ];
        $DB->insert_record('block_closed_loop_reply', $insert);
    }
}
if ($USER->id != $record->userid) {
    $insert = [
        'courseid' => $record->courseid,
        'requestid' => $requestid,
        'userid' => $record->userid,
    ];
    $DB->insert_record('block_closed_loop_reply', $insert);
}
header("Location: /blocks/closed_loop_support/request_detail.php?requestid=$requestid");