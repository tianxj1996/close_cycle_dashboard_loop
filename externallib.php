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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

class block_closed_loop_support_external_data extends external_api {
    
    /**
    * Parameters definition for update_db (user not required here)
    */
    public static function update_db_parameters() {
        return new external_function_parameters(
            array(
                'courseID' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'cmID' => new external_value(PARAM_INT, 'id of course module', VALUE_REQUIRED)
            )
        );
    }
    
        /**
    * Returns a log message
    *
    * @return external_description
    */
    public static function update_db_returns() {
        return new external_value(PARAM_TEXT, 'Test');
    }
    
    
    public static function update_db(int $courseID, int $cmID){
        global $DB, $USER;
        
        $table = 'block_closed_loop_support';
        $conditions = array('courseid' => $courseID, 'moduleid' => $cmID, 'userid' => $USER->id);
        if(!$DB->record_exists($table, $conditions))
        {
            $dataobject = array(
                'userid' => $USER->id,
                'courseid' => $courseID,
                'moduleid' => $cmID,
                'counter' => 1
            );
            $newID = $DB->insert_record($table, $dataobject);
        }
        else
        {
            $actualCounter = $DB->get_field($table, 'counter', $conditions);
            $DB->set_field($table, 'counter', $actualCounter+1, $conditions);
        }
        
    }
}