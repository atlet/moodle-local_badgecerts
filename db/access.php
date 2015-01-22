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
 * Capability definitions for this plugin.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    // View available badge certificates without using them.
    'local/badgecerts:viewcertificates' => array(
        'captype'       => 'read',
        'contextlevel'  => CONTEXT_COURSE,
        'archetypes'    => array(
            'user'          => CAP_ALLOW,
        )
    ),

    // Create/duplicate badge certificates.
    'local/badgecerts:createcertificate' => array(
        'riskbitmask'  => RISK_SPAM,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    // Delete badge certificates.
    'local/badgecerts:deletecertificate' => array(
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    // Set up/edit badge certificate details.
    'local/badgecerts:configurecertificate' => array(
        'riskbitmask'  => RISK_SPAM,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    // Set up/edit badge certificate elements.
    'local/badgecerts:configureelements' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    // Access/use "official" badge certificates.
    'local/badgecerts:assignofficialcertificate' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
        )
    ),

    // Access/use non "official" badge certificates only.
    'local/badgecerts:assigncustomcertificate' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),
    
    // View badge certificates.
    'local/badgecerts:viewcertificates' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),
    
    // Print badge certificates.
    'local/badgecerts:printcertificates' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),
    
    // Print badge certificates.
    'local/badgecerts:certificatemanager' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'manager'        => CAP_ALLOW,
        )
    ),

);
