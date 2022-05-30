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
 * Form for response definition
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once("$CFG->libdir/formslib.php");

 class setresponse_form extends moodleform
{
     
        /**
        * Extended Constructor
        * @param int $courseid
        * @param int $sectionid 
        * @param int $moduleid 
        * @param string $url
        */
        function __construct($courseid, $sectionid, $moduleid, $url) {
            global $CFG;
            $this->courseid = $courseid;
            $this->sectionid = $sectionid;
            $this->moduleid = $moduleid;
            parent::__construct($url);
        }
        
        
        /**
        * Defining the form content
        */
        public function definition()
        {
            global $CFG,$DB;
            
            $course = get_course($this->courseid);
            $cms = get_fast_modinfo($this->courseid);
            $cm = $cms->get_cm($this->moduleid);

            $mform = $this->_form;
            $mform->addElement('header', 'general_header', get_string('setResponse', 'block_closed_loop_support'));
            $mform->addElement('static', 'description', 'Course', $course->fullname);
            $mform->addElement('static', 'description', 'Section', get_section_name($this->courseid, $this->sectionid));
            $mform->addElement('static', 'description', 'Module', $cm->get_formatted_name());
            
            
            $mform->addElement('advcheckbox', 'response_active', 'Response is active', 
                    'Active', array('group' => 1), array(0, 1));
            $mform->addHelpButton('response_active', 'responseActive', 'block_closed_loop_support');
            
            $mform->addElement('select', 'explanation_forwarding', 'Explanation for request', 
                    array(0 => 'No', 1 => 'Optional', 2 => 'Mandatory'));
            $mform->setDefault('explanation_forwarding', 1);
            $mform->addHelpButton('explanation_forwarding', 'explanationForwarding', 'block_closed_loop_support');
            

            $context = context_course::instance($this->courseid);
            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $context);
            $mform->addElement(
            'editor',
            "config_text",
            'Define content',
             null,
             $editoroptions
             );
            $mform->setType('config_text', PARAM_RAW);
            $mform->addHelpButton('config_text', 'responseContent', 'block_closed_loop_support');
            
            $mform->addElement('select', 'response_size', 'Size of response dialog', array(0 => 'Normal', 1 => 'Big'));
            $mform->addHelpButton('response_size', 'responseSetSize', 'block_closed_loop_support');
            $this->add_action_buttons();
        }
}
