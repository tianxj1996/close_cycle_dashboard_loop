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
 * Database upgrade.
 *
 * @package    block_closed_loop_support
 * @copyright  2022 Rene Hilgemann
 * @author     Rene Hilgemann <rene.hilgemann@stud.uni-due.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Performs database actions to upgrade from older versions, if required.
 * @param int $oldversion Plugin version we are upgrading from.
 * @return boolean
 */
function xmldb_block_closed_loop_support_upgrade($oldversion) {
    global $DB, $pluginVersion;
    
    $dbman = $DB->get_manager();
 
    if ($oldversion < $pluginVersion) {
        // Define field id to be added to block_closed_loop_support.
        $table = new xmldb_table('block_closed_loop_support');
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Closed_loop_support savepoint reached.
        upgrade_block_savepoint(true, $pluginVersion, 'closed_loop_support');
    }

    return true;
}