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
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */




import * as Templates from 'core/templates';
import * as Notification from 'core/notification';
import * as Ajax from 'core/ajax';
import * as ModalFactory from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';
import * as Str from 'core/str';


//The modal dialog object
var modalObj;
var courseidClicked;
var moduleidClicked;
var actualCounter;
//var isMandatory;

//Spinner for time during loading
const spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';

/**
 * Ask for response and render in div element inside of block
 * @param {Integer} courseid
 * @param {Integer} moduleid
 */
const renderResponse = (courseid, moduleid) => {

    Ajax.call([{
        methodname: 'block_closed_loop_support_get_modal_body',
        args: {
            courseid: courseid,
            moduleid: moduleid},
        done: function(values) {
                if(modalObj !== null){
                    modalObj.setBody(values.modalbody);
                    if(values.size){
                        modalObj.setLarge();
                    }
                    else{
                        modalObj.setSmall();
                    }
                }
                else{
                    Notification.exception(new Error('Failed to load modal dialog base'));
                }
            },
        fail: Notification.exception
      }]);
};

/**
 *
 * @returns {undefined}
 */
const loadModalDialog = () =>{
    Str.get_string('responseTitle', 'block_closed_loop_support').then(function(title) {
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
                modal.getRoot().on('click', '#id_submit_button', processSubmit);
                modal.getRoot().on(ModalEvents.hidden, function(){
                    //Destroy and not only hide
                    modal.destroy();
                });
                writeRequest();
        });
    }).catch(function() {
            Notification.exception(new Error('Failed to load HeaderTitle'));
    });
};

/**
 *
 * @param {Object} e
 * @returns {undefined}
 */
const processSubmit = (e) =>{
    e.preventDefault();
    var value = document.getElementById('id_explanation_textarea').value;
    if(value.length === 0){
        document.getElementById('no_empty_explanation_label').style = 'display: visible';
    }
    else
    {
        document.getElementById('content_modal_body_elem').style = 'display: visible';
        Ajax.call([{
            methodname: 'block_closed_loop_support_write_explanation',
            args: {
                courseid: courseidClicked,
                cmid: moduleidClicked,
                counter: actualCounter,
                explanation: value},
            done: explanationWasSend(),
            fail: Notification.exception
        }]);
    }


};


const explanationWasSend = () =>{
    Str.get_string('expThanks', 'block_closed_loop_support').then(function(thanks) {
            document.getElementById('explain_heading_label').innerHTML = "<b>"+ thanks +"<b>";
            var nodes = document.getElementById("explanation_modal_body_elem").getElementsByTagName('*');
            for(var i = 0; i < nodes.length; i++){
                nodes[i].disabled = true;
            }
    }).catch(function() {
        Notification.exception(new Error('Failed to load HeaderTitle'));
    });
};

const writeRequest = () =>{
        Ajax.call([{
            methodname: 'block_closed_loop_support_write_requests',
            args: {
                courseid: courseidClicked,
                cmid: moduleidClicked},
            done: function(counter) {
                    actualCounter = counter;
                    renderResponse(courseidClicked, moduleidClicked);
                },
            fail: Notification.exception
        }]);
};
/**
 * Click event
 * @param {Var} event
 */
const buttonClickEvent = (event) =>{

    var stringIDs = event.target.getAttribute('id').replace('loopButton_','');
    var splitString = stringIDs.split('_');
    courseidClicked = splitString[0];
    moduleidClicked = splitString[1];
    loadModalDialog();
};


/**
 * Insert element
 * @param {Var} element
 * @param {Var} data
 */
const renderButtonTemplate = (element, data) => {

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
            moduleid: Object.values(requestButtons)[i].moduleid,
            tooltip: Object.values(requestButtons)[i].tooltip
        };

        /**
        General idea for placing button into 'module-<X>'
        and mouseover effect
        adapted from moodle plugin 'Point of view - Feedback'
        developed by: Quentin Fombaron and Astor Bizard
        https://moodle.org/plugins/block_point_view
        Release v1.6.3
        */
        var element = document.getElementById('module-' + data.moduleid);
        if(element)
        {
            renderButtonTemplate(element, data);
        }
    }


};

