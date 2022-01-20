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
 * Reload the list if something happend (like li-count changes)
 * @copyright  2022 Rene Hilgemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Ajax from 'core/ajax';

/**
 * Update block for teacher
 * @param {Var} responseList as html
 */
function renderTemplate(responseList){

    var element = document.getElementById('define_response_list');
    if(element){
        element.innerHTML = responseList;
    }
}


/**
 * Loop function
 * @param {Integer} courseid
 * @param {Integer} oldNumber
 */
function loopReload(courseid, oldNumber){
     var oldNum = oldNumber;
     var id = courseid;
     var elements = document.getElementsByClassName('course-content');
     if(elements.length > 0){
         if(elements[0].getElementsByTagName('li').length !== oldNum){
                oldNum = elements[0].getElementsByTagName('li').length;
                Ajax.call([{
                    methodname: 'block_closed_loop_support_get_responselist_html',
                    args: {courseid: courseid},
                    done: function(responseList) {
                            renderTemplate(responseList);
                        },
                    fail: Notification.exception
      }]);
         }
     }
     setTimeout(loopReload, 1000, id, oldNum);
}


/**
 * Setting up
 * @param {Integer} courseid
 */

export const init = (courseid) => {

     var elements = document.getElementsByClassName('course-content');
     if(elements.length > 0){
         loopReload(courseid, elements[0].getElementsByTagName('li').length);
     }

};