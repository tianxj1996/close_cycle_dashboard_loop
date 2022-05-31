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



class reply_explanation_form extends moodleform
{

    /**
     * Extended Constructor
     * @param int $requestid
     * @param string $url
     */
    function __construct($requestid, $url) {
        $this->requestid = $requestid;
        parent::__construct($url);
    }


    /**
     * Defining the form content
     */
    public function definition()
    {
        $mform = $this->_form;

        $mform->addElement('editor', 'explanation_textarea', get_string('explainFormGeneral', 'block_closed_loop_support'),
            'wrap="virtual" rows="10" cols="50"');
        $mform->addElement('html',
            '<input name="requestid" value="' . $this->requestid . '" type="hidden">');

        $mform->addElement('submit', 'submit_button', get_string('submit'));

        $mform->addElement('html',
            '<div id="no_empty_explanation_label" class="fdescription required" style="display: none">'
            . 'No empty explanation aloud <i class="icon fa fa-exclamation-circle text-danger fa-fw " '
            . 'title="Empty explanation" aria-label="Empty explanation"></i>.</div>');
    }
}
