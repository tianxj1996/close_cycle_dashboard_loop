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
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class request_table extends table_sql {


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
        $columns = array('pic','username', 'firstname', 'lastname', 'courseid', 
            'moduleid', 'timestamp', 'counter', 'explanation');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('Profile picture', 'Username', 'First name', 'Last name', 
            'Course', 'Course module', 'Time', 'Counter', 'Explanation');
        $this->define_headers($headers);
        $this->is_sortable = false;
        $this->is_collapsible = false;
        $this->sort_default_column = 'timestamp';
        $this->sort_default_order = SORT_DESC;
    }

    /**
     * Show the column 'pic' with link to user-profile
     * @param object $values Contains object with all the values of record.
     * @return $string Return html for picture
     */
    function col_pic($values) {
        global $OUTPUT, $DB;
        if ($this->is_downloading()) {
            return $values->username;
        } else {
            $user = $DB->get_record('user', array('id' => $values->userid));
            $original = $OUTPUT->user_picture($user, array('size'=>30));
            $newWithTooptip = str_replace("<img src", "<img title='Go to profile' src", $original);
            return $newWithTooptip;
        }
    }
    
    /**
     * Extend class for row with new values
     * @param type $row
     * @return string css-class
     */
    function get_row_class($row) {
        if(in_array($row->id, $this->unreadRequests)){
            return 'cell_color';
        }
        else{
            return '';
        }
    }
    
     /**
     * Show time in standard time format of moodle
     * @param object $values Contains object with all the values of record.
     * @return $string Return time as date-string
     */
    function col_timestamp($values) {
        return date("Y-m-d H:i:s", $values->timestamp);
    }
    
     /**
     * Show the column username with link to user-profile
     * @param object $values Contains object with all the values of record.
     * @return $string Return html for link
     */
    function col_username($values) {
        if ($this->is_downloading()) {
            return $values->username;
        } else {
            $url = new moodle_url('/user/view.php', array('id' => $values->userid, 'course' => $values->courseid));
            return html_writer::link($url, $values->username, ['title' => 'Go to Profile']);
        } 
    }
    
     /**
     * Show the module with formated name
     * @param object $values Contains object with all the values of record.
     * @return $string Return formated name of moduleid
     */
    function col_moduleid($values){
        $cms = get_fast_modinfo($values->courseid);
        $cm = $cms->get_cm($values->moduleid);
        return $cm->get_formatted_name();
    }
    
    /**
     * Show the shortname of course
     * @param object $values Contains object with all the values of record.
     * @return $string Return shortname of courseid
     */
    function col_courseid($values){
        $course = get_course($values->courseid);
        return $course->shortname;
    }
    
    /**
     * Show the explanation with button to modal
     * @param object $values Contains object with all the values of record.
     * @return $string Return shortname of courseid
     */
    function col_explanation($values){
        global $OUTPUT;
        $requestID = $values->id;
        $disabled = $values->explanationsend == 0 ? 'disabled' : 'enabled';
        $titleText = get_string($values->explanationsend == 1 ? 
                'overviewExplainYes' : 'overviewExplainNo', 'block_closed_loop_support');
        $data = ['requestID' => $requestID, 'disabled' => $disabled, 'titleText' => $titleText];
        return $OUTPUT->render_from_template('block_closed_loop_support/showExplanation', $data);
    }
    

}