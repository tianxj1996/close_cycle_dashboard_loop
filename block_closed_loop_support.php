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
                'site-index' => true,
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

        if(!$this->page->user_is_editing()){
            if(has_capability('block/closed_loop_support:access_requests', $this->context)){
                $data = block_closed_loop_support_get_new_requests_teacher($USER->id, $COURSE->id);
                $this->content->text = 
                        $OUTPUT->render_from_template('block_closed_loop_support/requestLink', $data);
            }
            else if(has_capability('block/closed_loop_support:generate_requests', $this->context)){
                return null;
            }
        }
        else{
            $startSetResponse = get_string('defResModule', 'block_closed_loop_support');
            if(has_capability('block/closed_loop_support:add_response', $this->context) 
                    && $this->page->context->contextlevel == CONTEXT_COURSE){
                $this->content->text = 
                        $startSetResponse . 
                        html_writer::start_div('', ['id' => 'define_response_list']). 
                        block_closed_loop_support_get_responselist_html($COURSE->id).
                        html_writer::end_div();
            }
            else{
                if($this->page->context->contextlevel != CONTEXT_COURSE){
                      $this->content->text = get_string('defNoRespAddable', 'block_closed_loop_support');
                }
                else if(!has_capability('block/closed_loop_support:add_response', $this->context)){
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

        if(!$this->page->user_is_editing()){
           if(has_capability('block/closed_loop_support:access_requests', $this->context)){
             $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_request_link_update', 
                    'init', [$COURSE->id]);
            }
            if(has_capability('block/closed_loop_support:generate_requests', $this->context)){
                $param = block_closed_loop_support_get_responselist($COURSE->id);
                $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_support', 
                    'init', [$param]);
            }
        }
        else{
            if(has_capability('block/closed_loop_support:add_response', $this->context)){
                $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_list_reload', 
                    'init', [$COURSE->id]);
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