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
        $this->title = get_string('pluginname', 'block_closed_loop_support');
    }
    
        /**
     * Build block content.
     * @return Object
     */
    public function get_content() {
        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = "Block Closed loop support";
        return  $this->content;
    }
    
    /**
     * {@inheritDoc}
     * @see block_base::get_required_javascript()
     */
    public function get_required_javascript() {
        parent::get_required_javascript();

        global $COURSE , $USER, $OUTPUT;
        if(!$this->page->user_is_editing())
        {
            //Load course data
            
            
             //$button = "<button id='test' class='btn btn-success'>bla</button>";
             //$this->page->requires->js_init_code('document.getElementById("maincontent")
             //                                .after(' . $button . ');');
            \core\notification::success("Blub");
            

            //$buttonHtml =  $OUTPUT->render_from_template('block_closed_loop_support/loopButton', $data);
            $this->page->requires->js_call_amd('block_closed_loop_support/script_closed_loop_support', 
                    'init', [$COURSE->id]);

        }

    }
        
}