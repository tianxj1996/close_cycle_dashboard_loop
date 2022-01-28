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
 * Small form for explanation of help request
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 defined('MOODLE_INTERNAL') || die();
 
 //require_once(__DIR__.'/../../../config.php');
 require_once($CFG->libdir.'/formslib.php');
 


 class explanation_form extends moodleform
{
     
        /**
        * Extended Constructor
        * @param int $courseid
        * @param int $moduleid 
        * @param int $mandatory 
        * @param string $url
        */
        function __construct($courseid, $moduleid, $mandatory, $url) {
            $this->courseid = $courseid;
            $this->moduleid = $moduleid;
            $this->mandatory = $mandatory;
            parent::__construct($url);
        }
        
        
        /**
        * Defining the form content
        */
        public function definition()
        {
            
            $course = get_course($this->courseid);
            $cms = get_fast_modinfo($this->courseid);
            $cm = $cms->get_cm($this->moduleid);

            $mform = $this->_form;
            $textOpt = $this->mandatory <= 1 ? "Optional " : 'Mandatory ';
            $text2 = " " . ($this->mandatory == 2 ? get_string('explainHeading2', 'block_closed_loop_support') : "");
            $mform->addElement('html', 
                    html_writer::div(get_string('explainHeading', 'block_closed_loop_support', $textOpt) . $text2 
                            , 'form-description mb-3', ['id' => 'explain_heading_label']));
            
            $mform->addElement('textarea', 'explanation_textarea', get_string('explainFormGeneral', 'block_closed_loop_support'), 
                    'wrap="virtual" rows="2" cols="50"');
            $mform->addHelpButton('explanation_textarea', 'explainFormGeneral', 'block_closed_loop_support');
            
            
            $mform->addElement('submit', 'submit_button', get_string('submit'));
            
            $mform->addElement('html', 
                    '<div id="no_empty_explanation_label" class="fdescription required" style="display: none">'
                    . 'No empty explanation aloud <i class="icon fa fa-exclamation-circle text-danger fa-fw " '
                    . 'title="Empty explanation" aria-label="Empty explanation"></i>.</div>');
        }
}
