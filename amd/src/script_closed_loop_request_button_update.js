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


const data = {
    courseID: -1
};


/**
 * Setting up
 * @param {Integer} courseID
 */

export const init = (courseID) => {
    data.courseID = courseID;

    var overviewButton = document.getElementById('Request_Overview_Button');
    
}