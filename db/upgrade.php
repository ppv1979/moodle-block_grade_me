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
 * This file keeps track of upgrades to the self completion block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2013 Dakota Duff <http://www.remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Handles upgrading instances of this block.
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_grade_me_upgrade($oldversion, $block) {
    global $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2013022600) {
        // Get the instances of this block.
        if ($blocks = $DB->get_records('block_instances', array('blockname' => 'grade_me', 'pagetypepattern' => 'my-index'))) {
            // Loop through and remove them from the My Moodle page.
            foreach ($blocks as $block) {
                blocks_delete_instance($block);
            }

        }

        // grade_me savepoint reached
        upgrade_block_savepoint(true, 2013022600, 'grade_me');
    }

    if ($oldversion < 2013022603) {
        // Rename and redefine old field name 'itemid' from table table block_grade_me to 'id'.
        $table = new xmldb_table('block_grade_me');
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'id');
        }

        upgrade_block_savepoint(true, 2013022603, 'grade_me');
    }

    return true;
}
