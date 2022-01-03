// This file is part of Moodle - https://moodle.org/
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
 * Update button for analyzing if new requests are available
 * @copyright  2022 Rene Hilgemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Templates from 'core/templates';
import * as Notification from 'core/notification';
import * as Ajax from 'core/ajax';


/**
 * Update block for teacher
 * @param {Array} responseData
 */
function renderTemplate(responseData){

    var element = document.getElementById('Request_Overview_content_teacher');
    if(element){
        Templates.renderForPromise('block_closed_loop_support/requestLink', responseData)
         .then(({html, js}) => {
            Templates.replaceNode(element, html, js);
         }).catch();
    }

}

/**
 * Loop function
 * @param {Integer} courseid
 */
function loopRequest(courseid){
     var id = courseid;
     Ajax.call([{
        methodname: 'block_closed_loop_support_read_requests',
        args: {courseid: courseid},
        done: function(responseData) {
                renderTemplate(responseData);
            },
        fail: Notification.exception
      }]);
      setTimeout(loopRequest, 3000, id);
}

/**
 * Setting up
 * @param {Integer} courseid
 */

export const init = (courseid) => {

    var element = document.getElementById('Request_Overview_content_teacher');
    if(element){
        loopRequest(courseid);
    }

};

