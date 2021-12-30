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
 * Module to display and manage reactions and difficulty tracks on course page.
 * @copyright  2022 Rene Hilgemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Templates from 'core/templates';
import * as Notification from 'core/notification';
import * as Ajax from 'core/ajax';


const data = {
    courseID: -1
};


const buttonClickEvent = (e) =>{

    var cmIDClicked = e.target.getAttribute('id').replace('Module-','');

     Ajax.call([{
        methodname: 'block_closed_loop_support_write_requests',
        args: {
            courseid: data.courseID,
            cmid: cmIDClicked},
        done: Notification.addNotification({message: "Request forwarded", type: "info"}),
        fail: Notification.exception
      }]);


};


/**
 * Insert element
 * @param {Var} element
 * @param {Integer} idNumber
 */
const renderTemplate = (element, idNumber) => {
        var context = {
            Text: 'Help',
            ModulNumber: idNumber
        };
        context.ModulNumber = idNumber;
        Templates.renderForPromise('block_closed_loop_support/loopButton', context)
                .then(({html, js}) => {
            Templates.prependNodeContents(element, html, js);
            }).catch();
};

/**
 * Setting up
 * @param {Integer} courseID
 */
export const init = (courseID) => {
    data.courseID = courseID;

    var courseWrapper = document.getElementsByClassName('course-content');
    if(courseWrapper)
    {
        courseWrapper[0].addEventListener('click', (e) => {buttonClickEvent(e);});
    }

    for(let i = 1; i < 10; i++)
    {
        var element = document.getElementById('module-' + i);
        if(element)
        {
            renderTemplate(element, i);
        }

    }

};

