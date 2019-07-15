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
 * @copyright  2014 onwards Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'local_badgecerts' => [ // Plugin identifier
        'handlers' => [ // Different places where the plugin will display content.
            'badgecertificateslist' => [ // Handler unique name (alphanumeric).
                'displaydata' => [
                    'title' => 'mybadgecertificates',
                    'icon' => '/pix/t/award.png',
                    'class' => '',
                ],

                'delegate' => 'CoreMainMenuDelegate', // Delegate (where to display the link to the plugin)
                'method' => 'mobile_badgecerts_list', // Main function in \mod_certificate\output\mobile
                'offlinefunctions' => [
                ], // Function that needs to be downloaded for offline.
            ],
        ],
        'lang' => [ // Language strings that are used in all the handlers.
            ['mybadgecertificates', 'local_badgecerts'],
        ],
    ],
];