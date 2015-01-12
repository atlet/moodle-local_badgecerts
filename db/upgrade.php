<?php

// This file is part of the BadgeCerts plugin for Moodle - http://moodle.org/
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
 * Language file for 'local_badgecerts' plugin.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_local_badgecerts_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    if ($oldversion < 2014090900) {
        // Add tables for certificates based on openbadges.
        // Define table 'badge_certificate' to be created.
        $table = new xmldb_table('badge_certificate');

        // Adding fields to table 'badge_certificate'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('official', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'name');
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'official');
        $table->add_field('certbgimage', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'description');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'bgimage');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'timemodified');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'usercreated');
        $table->add_field('issuername', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'usermodified');
        $table->add_field('issuercontact', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'issuername');
        $table->add_field('format', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'A4', 'issuercontact');
        $table->add_field('orientation', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, 'P', 'format');
        $table->add_field('unit', XMLDB_TYPE_CHAR, '2', null, XMLDB_NOTNULL, null, 'mm', 'orientation');
        $table->add_field('type', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'unit');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'type');
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'courseid');
        $table->add_field('nextcron', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'status');

        // Adding keys to table 'badge_certificate'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->add_key('fk_usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
        $table->add_key('fk_usercreated', XMLDB_KEY_FOREIGN, array('usercreated'), 'user', array('id'));

        // Adding indexes to table 'badge_certificate'.
        $table->add_index('type', XMLDB_INDEX_NOTUNIQUE, array('type'));

        // Conditionally launch create table for 'badge_certificate'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table 'badge_certificate_elms' to be created.
        $table = new xmldb_table('badge_certificate_elms');

        // Adding fields to table 'badge_certificate_elms'.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('certid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('x', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'certid');
        $table->add_field('y', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'x');
        $table->add_field('size', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '12', 'y');
        $table->add_field('family', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, 'size');
        $table->add_field('rawtext', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'family');
        $table->add_field('align', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'rawtext');

        // Adding keys to table 'badge_certificate_elms'.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_certid', XMLDB_KEY_FOREIGN, array('certid'), 'badge_certificate', array('id'));

        // Conditionally launch create table for 'badge_certificate_elms'.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field variant to be added to badge.
        $table = new xmldb_table('badge');
        $field = new xmldb_field('certid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'courseid');

        // Conditionally launch add field variant.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014090900);
    }

    if ($oldversion < 2014102804) {
        // Add additional field to 'badge_certificate' table.
        $table = new xmldb_table('badge_certificate');
        $field = new xmldb_field('bookingid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'certbgimage');

        // Conditionally launch add field 'bookingid'.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Create custom profile field, accessible only to administrators
        // Administrators can enter in which (only one) course the selected
        // user will have 'bulk generate badge certificates' privileges.
        $category = $DB->get_record('user_info_category', array('sortorder' => 1));
        $rec = new StdClass();
        $rec->shortname = 'bulkGenCerts';
        $rec->name = 'Bulk generate badge certificates in course';
        $rec->datatype = 'text';
        $rec->descriptionformat = 1;
        $rec->categoryid = $category->id;
        $rec->visible = 0; // Visible only to admins
        $rec->defaultdata = 0;
        $rec->param1 = 10;
        $rec->param2 = 10;
        $DB->insert_record('user_info_field', $rec);

        // Drop table 'badge_certificate_elms' since it's not used anymore.
        $targettablename = 'badge_certificate_elms';
        if ($dbman->table_exists($targettablename)) {
            $table = new xmldb_table($targettablename);
            $dbman->drop_table($table); // And drop it
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2014102804);
    }

    if ($oldversion < 2015011200) {

        // Define table badge_certificate_trasnfers to be created.
        $table = new xmldb_table('badge_certificate_trasnfers');

        // Adding fields to table badge_certificate_trasnfers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgecertificateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table badge_certificate_trasnfers.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table badge_certificate_trasnfers.
        $table->add_index('badgecertificateid', XMLDB_INDEX_NOTUNIQUE, array('badgecertificateid'));
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch create table for badge_certificate_trasnfers.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Badgecerts savepoint reached.
        upgrade_plugin_savepoint(true, 2015011200, 'local', 'badgecerts');
    }

    if ($oldversion < 2015011201) {

        // Define field transfereruserid to be added to badge_certificate_trasnfers.
        $table = new xmldb_table('badge_certificate_trasnfers');
        $field = new xmldb_field('transfereruserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null,
                'created');

        // Conditionally launch add field transfereruserid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('transfereruserid', XMLDB_INDEX_NOTUNIQUE, array('transfereruserid'));

        // Conditionally launch add index transfereruserid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Badgecerts savepoint reached.
        upgrade_plugin_savepoint(true, 2015011201, 'local', 'badgecerts');
    }

    return true;
}
