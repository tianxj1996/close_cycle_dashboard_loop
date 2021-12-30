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
 * block_closed_loop_support class
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@gmx.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_closed_loop_support extends block_base{
     

    /**
     * Block initialization
     */
    public function init() {
        global $PAGE;
        $this->title = get_string('pluginname', 'block_closed_loop_support');
        
        

        

    }
    
    
    /**
    * Define where block should be addable 
    *
    * @return array
    */
    public function applicable_formats() {
        return array(
            'course-view' => true
        );
    }
    
        /**
     * Build block content.
     * @return Object
     */
    public function get_content() {
        global $PAGE, $DB, $USER, $COURSE, $CFG; //TODO Check if really a course!
        require_once(__DIR__ . '/locallib.php');
        
        $this->content = new stdClass();
        //$this->content->footer = 'Footer';
        $showButtonRequests = false;

        $blockText = '<div style="text-align:center">';
        
        if(has_capability('block/closed_loop_support:access_requests', $this->context)){
            //We are no student
            $showButtonRequests = true;
            $newRequests = false;
            $requests = block_closed_loop_support_get_new_requests_teacher($USER->id);
            if(!$requests){
                $blockText .= get_string('noRequest', 'block_closed_loop_support');
            }
            else if (count($requests) > 1){
                $newRequests = true;
                $blockText .= get_string('newRequests', 'block_closed_loop_support');
            }
            else
            {
                $newRequests = true;
                $blockText .= get_string('newRequest', 'block_closed_loop_support');
            }
            $blockText = $blockText . '<br><br>';
            
            if($showButtonRequests){
                $bClass = 'btn-warning';
                if($newRequests){
                    $bClass = 'btn-warning';
                }
                else{
                    $bClass = 'btn-warning';
                }
                $blockText .= html_writer::link(
                            new moodle_url("{$CFG->wwwroot}/blocks/closed_loop_support/request_overview.php", array('courseid'=> $COURSE->id)),
                            " <button id='Request_Overview_Button' class='btn $bClass'>Request overview</button>");
            }
            $blockText .= '</div>';
            $this->content->text = $blockText;

        }
        else{
            $this->content->text = 'You are a student!'; //TODO!
        }
        return  $this->content;
    }
    
    /**
     * {@inheritDoc}
     * @see block_base::get_required_javascript()
    */
    public function get_required_javascript() {
        parent::get_required_javascript();

        global $COURSE , $USER, $OUTPUT;
        $modinfo = get_fast_modinfo($COURSE->id);
        $test = $modinfo->get_used_module_names();
        $x = current($test);
        
        if(!$this->page->user_is_editing() && has_capability('block/closed_loop_support:access_requests', $this->context)){
             $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_request_button_update', 
                    'init', [$COURSE->id]);
        }
        
        if(!$this->page->user_is_editing() && has_capability('block/closed_loop_support:access_requests', $this->context))
        {
            //Load course data
            $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_support', 
                    'init', [$COURSE->id]);

        }

    }
        
}