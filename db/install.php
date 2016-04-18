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
 * Code for installing the plugin.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_badgecerts_install() {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    // Define field variant to be added to badge.
    $table = new xmldb_table('badge');
    $field = new xmldb_field('certid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'courseid');

    // Conditionally launch add field variant.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

}
