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
 * External Web Service Template
 *
 * @package    local_badgecerts
 * @copyright  2014 Andraž Prinčič s.p. (http://www.princic.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("../../config.php");
require_once("$CFG->dirroot/local/badgecerts/lib.php");
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "/filelib.php");
require_once($CFG->libdir . "/datalib.php");

class local_badgecerts_external extends external_api {

    /**
     * Returns description of method parameters
     * @return local_badgecerts_get_certificates
     */
    public static function get_certificates_parameters() {
        return new external_function_parameters(
                array('courseid' => new external_value(PARAM_INT, 'Course id', VALUE_DEFAULT, 0))
        );
    }

    /**
     * Return certificates for selected courseid
     * @return ...
     */
    public static function get_certificates($courseid = 0) {
        $context = context_course::instance($courseid);
        self::validate_context($context);

        return get_all_certificates($courseid);
    }

    /**
     * Returns description of method result value
     * @return local_badgecerts_get_certificates
     */
    public static function get_certificates_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                array(
            'recipientFirstName' => new external_value(PARAM_TEXT, 'Recipient first name'),
            'recipientLastName' => new external_value(PARAM_TEXT, 'Recipient last name'),
            'recipientEmail' => new external_value(PARAM_TEXT, 'Recipient email'),
            'issuerName' => new external_value(PARAM_TEXT, 'Issuer name'),
            'issuerContact' => new external_value(PARAM_TEXT, 'Issuer contact'),
            'badgeName' => new external_value(PARAM_TEXT, 'Badge name'),
            'badgeDesc' => new external_value(PARAM_TEXT, 'Badge desc'),
            'badgeNumber' => new external_value(PARAM_INT, 'Badge number'),
            'badgeCourse' => new external_value(PARAM_TEXT, 'Badge course'),
            'badgeHash' => new external_value(PARAM_TEXT, 'Badge hash'),
            'bookingTitle' => new external_value(PARAM_TEXT, 'Booking title'),
            'bookingStartdate' => new external_value(PARAM_TEXT, 'Booking start date'),
            'bookingEnddate' => new external_value(PARAM_TEXT, 'Booking end date'),
            'bookingDuration' => new external_value(PARAM_TEXT, 'Booking duration'),
            'recipientBirthdate' => new external_value(PARAM_TEXT, 'Recipient birthdate'),
            'recipientInstitution' => new external_value(PARAM_RAW, 'Recipient institution'),
            'badgeDateIssued' => new external_value(PARAM_TEXT, 'Badge date issued')
                )
                )
        );
    }
}