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
 * Manage buttons and response for closed loop support
 * @copyright  2022 Rene Hilgemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Templates from 'core/templates';
import * as Notification from 'core/notification';
import * as Ajax from 'core/ajax';
import * as ModalFactory from 'core/modal_factory';


/**
 * Ask for response and render in div element inside of block
 * @param {Integer} courseid
 * @param {Integer} moduleid
 */
const renderResponse = (courseid, moduleid) => {
    Ajax.call([{
        methodname: 'block_closed_loop_support_get_response_content',
        args: {
            courseid: courseid,
            moduleid: moduleid},
        done: function(responseContent) {
                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: responseContent.title,
                    body: responseContent.content,
                    footer: '',
                    large: true,
                    scrollable: false
                })
                .then(modal => {
                    modal.show();
                    return modal;
                });

            },
        fail: Notification.exception
      }]);
};

/**
 * Click event
 * @param {Var} element
 */
const buttonClickEvent = (element) =>{

    var stringIDs = element.target.getAttribute('id').replace('loopButton_','');
    var splitString = stringIDs.split('_');
     Ajax.call([{
        methodname: 'block_closed_loop_support_write_requests',
        args: {
            courseid: splitString[0],
            cmid: splitString[1]},
        done: function() {
                renderResponse(splitString[0], splitString[1]);
            },
        fail: Notification.exception
      }]);
};


/**
 * Insert element
 * @param {Var} element
 * @param {Var} data
 */
const renderTemplate = (element, data) => {

        Templates.renderForPromise('block_closed_loop_support/loopButton', data)
                .then(({html, js}) => {
            var res = Templates.prependNodeContents(element, html, js);
            if(res !== null){
                res[0].addEventListener('click', (e) => {buttonClickEvent(e);});
            }
            }).catch();

};

/**
 * Setting up
 * @param {Array} requestButtons
 */
export const init = (requestButtons) => {

    for(let i = 0; i < Object.values(requestButtons).length; i++){
        const data = {
            courseid: Object.values(requestButtons)[i].courseid,
            moduleid: Object.values(requestButtons)[i].moduleid
        };

        var element = document.getElementById('module-' + data.moduleid);
        if(element)
        {
            renderTemplate(element, data);
        }
    }


};

