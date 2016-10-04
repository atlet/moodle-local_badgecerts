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
 * Code for uninstalling the plugin.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_badgecerts_uninstall() {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    // Define field variant to be added to badge.
    $table = new xmldb_table('badge');
    $field = new xmldb_field('certid');

    // Drop field (the function checks if it exists)
    $dbman->drop_field($table, $field);

    // Delete all the capabilities
    $capabilities = array(
        'moodle/badges:viewcertificates',
        'moodle/badges:createcertificate',
        'moodle/badges:deletecertificate',
        'moodle/badges:configurecertificate',
        'moodle/badges:configureelements',
        'moodle/badges:assignofficialcertificate',
        'moodle/badges:assigncustomcertificate'
    );
    $DB->delete_records_list('capabilities', 'name', $capabilities);
}
