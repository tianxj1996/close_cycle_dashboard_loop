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
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
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
                'admin' => false,
                'site-index' => false,
                'course-view' => true,
                'mod' => false,
                'my' => true
        );
    }
    
     /**
     * Build block content.
     * @return Object
     */
    public function get_content() {
        global $PAGE, $DB, $USER, $COURSE, $CFG, $OUTPUT;
        require_once(__DIR__ . '/locallib.php');
        $this->content = new stdClass();
        $startSetResponse = get_string('defResModule', 'block_closed_loop_support');
        
        //First case: We are on the dashboard and only 'myaddinstance' is relevant
        if($this->page->context->contextlevel == CONTEXT_USER){

            if(!has_capability('block/closed_loop_support:myaddinstance', $this->context)){
                return null;
            }
            
            if(!$this->page->user_is_editing()){
                $data = block_closed_loop_support_get_new_requests_teacher($USER->id, -1);
                $this->content->text = 
                        $OUTPUT->render_from_template('block_closed_loop_support/requestLink', $data);
            }
            else{
                $this->content->text = $startSetResponse . get_string('defNoRespAddable', 'block_closed_loop_support');
            }
            return  $this->content;
        }
        
        //Second case: We are on a course page
        if ($this->page->context->contextlevel == CONTEXT_COURSE){
            
            if(!$this->page->user_is_editing()){
                
                if(has_capability('block/closed_loop_support:access_requests', $this->context)){
                    $data = block_closed_loop_support_get_new_requests_teacher($USER->id, $COURSE->id);
                    $this->content->text = 
                        $OUTPUT->render_from_template('block_closed_loop_support/requestLink', $data);
                }
                else{
                    return null;
                }
            }
            else{
                if(has_capability('block/closed_loop_support:add_response', $this->context) 
                    && $this->page->context->contextlevel == CONTEXT_COURSE){
                    $this->content->text = 
                            $startSetResponse . 
                            html_writer::start_div('', ['id' => 'define_response_list']). 
                            block_closed_loop_support_get_responselist_html($COURSE->id).
                            html_writer::end_div();
                }
                else{
                    $this->content->text = $startSetResponse . get_string('defMissingCapabilitys', 'block_closed_loop_support');
                }
            }
        }
        return  $this->content;
    }
    
    /**
     * {@inheritDoc}
     * @see block_base::get_required_javascript()
    */
    public function get_required_javascript() {
        parent::get_required_javascript();

        global $COURSE;
        require_once(__DIR__ . '/locallib.php');
        
        
        //First case: We are on the dashboard and only 'myaddinstance' is relevant
        if($this->page->context->contextlevel == CONTEXT_USER &&
                has_capability('block/closed_loop_support:myaddinstance', $this->context)){
            
            if(!$this->page->user_is_editing()){
                
                if(has_capability('block/closed_loop_support:access_requests', $this->context)){
                    $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_request_link_update', 
                    'init', [-1]);
                }
            }
        }
        //We are in a course
        else if ($this->page->context->contextlevel == CONTEXT_COURSE ){
            
            if($this->page->user_is_editing()){
                
                if(has_capability('block/closed_loop_support:add_response', $this->context)){
                    $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_list_reload', 
                    'init', [$COURSE->id]);
                }
            }
            else{
                if(has_capability('block/closed_loop_support:generate_requests', $this->context)){
                    $param = block_closed_loop_support_get_responselist($COURSE->id);
                    $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_support', 
                        'init', [$param]);
                }
                
                if(has_capability('block/closed_loop_support:access_requests', $this->context)){
                    $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_request_link_update', 
                    'init', [$COURSE->id]);
                }
            }
        }
    }
    
    /**
     * Additional todos after instance created
     * @return boolean
     */
    function instance_create() {
        global $COURSE;
        require_once(__DIR__ . '/locallib.php');
        
        //If old values exist, delete them
        if($this->page->context->contextlevel == CONTEXT_COURSE){
            block_closed_loop_support_delete_response($COURSE->id);
            block_closed_loop_support_create_response($COURSE->id);
        }
        return true;
    }
    
    /**
     * Additional todos after instance deleted
     * @return boolean
     */
    function instance_delete() {
        global $COURSE;
        require_once(__DIR__ . '/locallib.php');
        
        if($this->page->context->contextlevel == CONTEXT_COURSE){
            block_closed_loop_support_delete_response($COURSE->id);
        }
        return true;
    }
}