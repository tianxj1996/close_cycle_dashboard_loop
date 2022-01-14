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
 * request_table class 
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@gmx.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class request_table extends table_sql {

    /**
     * 
     * @var type last Time  of check (-1 == never)
     */
    public $unreadRequests = [];
    public $courseid = -1;
    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('pic','username', 'firstname', 'lastname', 'courseid', 'moduleid', 'timestamp', 'counter');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('Profile picture', 'Username', 'First name', 'Last name', 'Course', 'Course modul','Time', 'Counter');
        $this->define_headers($headers);
        $this->is_sortable = false;
        $this->is_collapsible = false;
        $this->sort_default_column = 'timestamp';
        $this->sort_default_order = SORT_DESC;
    }

    /**
     * @param object $values Contains object with all the values of record.
     * @return $string Return username with link to profile or username only
     *     when downloading.
     */
    function col_pic($values) {
        // If the data is being downloaded than we don't want to show HTML.
        global $OUTPUT, $DB;
        if ($this->is_downloading()) {
            return $values->username;
        } else {
            $user = $DB->get_record('user', array('id' => $values->userid));
            return $OUTPUT->user_picture($user, array('size'=>30));
        }
    }
    
    /**
     * Extend class for row with new values
     * @param type $row
     * @return string
     */
    function get_row_class($row) {
        if(in_array($row->id, $this->unreadRequests)){
            return 'cell_color';
        }
        else{
            return '';
        }
    }
    
    function col_timestamp($values) {
        return date("Y-m-d H:i:s", $values->timestamp); //TODO: Correct format?
    }
    
    function col_username($values) {
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->username;
        } else {
            $url = new moodle_url('/user/profile.php', array('id' => $values->userid));
            return html_writer::link($url, $values->username);
        }
    }
    
    function col_moduleid($values){
        $cms = get_fast_modinfo($values->courseid);
        $cm = $cms->get_cm($values->moduleid);
        return $cm->get_formatted_name();
    }
    
    
    function col_courseid($values){
        $course = get_course($values->courseid);
        return $course->shortname;
    }
    

}