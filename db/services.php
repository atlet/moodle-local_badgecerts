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
 * Web service local plugin external functions and service definitions.
 *
 * @package    local_badgecerts
 * @copyright  2014 Andraž Prinčič s.p. (http://www.princic.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = [
        'local_badgecerts_get_certificates' => [
                'classname'   => 'local_badgecerts_external',
                'methodname'  => 'get_certificates',
                'classpath'   => 'local/badgecerts/externallib.php',
                'description' => 'Return all certificates for bookingid.',
                'type'        => 'read',
        ]
        ];

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Get certificates' => array(
                'functions' => array ('local_badgecerts_get_certificates'),
                'restrictedusers' => 0,
                'enabled' => 1,
        )
);
