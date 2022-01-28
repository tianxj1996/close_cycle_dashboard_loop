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
 * Show explanation in modal
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Ajax from 'core/ajax';
import * as ModalFactory from 'core/modal_factory';
import * as Str from 'core/str';
import * as Notification from 'core/notification';

var requestIDs;
var modalObj;

//Spinner for time during loading
const spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';


/**
 *
 * @param {Integer} reID
 * @returns {undefined}
 */
const getExplanation = (reID) => {
    Ajax.call([{
        methodname: 'block_closed_loop_support_get_explanation',
        args: {
            requestid: reID},
        done: function(explanationContent) {
                if(modalObj !== null){
                    modalObj.setBody(explanationContent);
                }
                else{
                    Notification.exception(new Error('Failed to load explanation, cause modal not found'));
                }
            },
        fail: Notification.exception
    }]);
};

/**
 *
 * @param {Integer} reID
 * @returns {undefined}
 */
const loadModal = (reID) =>{
    Str.get_string('explainShowTitle', 'block_closed_loop_support').then(function(title) {
        ModalFactory.create({
                    type: ModalFactory.types.CANCEL,
                    title: title,
                    body: spinner,
                    large: false,
                    scrollable: true
                    })
                    .then(modal => {
                        modal.setButtonText('cancel', 'Close');
                        modal.show();
                        modalObj = modal;
                        getExplanation(reID);
                    });
    }).catch(function() {
        Notification.exception(new Error('Failed to load HeaderTitle'));
    });
};

/**
 * Setting up
 * @param {Object} event
 */
const buttonClickEvent = (event) =>{
    var element = event.target;
    if(element.nodeName !== 'BUTTON'){
        return;
    }
    var elementID = element.getAttribute('id');
    if(!elementID.startsWith('explanationButton_')){
        return;
    }
    var reID = elementID.replace('explanationButton_', '');
    if(requestIDs.includes(reID)){
        loadModal(reID);
    }
};

/**
 * Setting up
 * @param {Array} requestids
 */
export const init = (requestids) => {
    requestIDs = requestids;
    document.addEventListener('click', (e) => {buttonClickEvent(e);});
};