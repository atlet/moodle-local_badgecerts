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
 * Mobile output class for certificate.
 *
 * @package   local_badgecerts
 * @copyright 2014 onwards Gregor Anželj, Andraž Prinčič
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */
namespace local_badgecerts\output;

use context_course;
use context_system;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/badgecerts/lib.php');

/**
 * Mobile output class for certificate.
 */
class mobile {
    /**
     * Returns the badgecerts view for the mobile app.
     * @param  array $args Arguments from tool_mobile_get_content WS
     *
     * @return array       HTML, javascript and otherdata
     */
    public static function mobile_badgecerts_list($args) {
        global $OUTPUT, $USER;

        $args = (object) $args;

        require_login();

        $certs = badges_get_user_certificates($USER->id, null);

        foreach ($certs as $key => $badge) {
            $context = ($badge->type == CERT_TYPE_SITE) ? context_system::instance() : context_course::instance($badge->courseid);
            $certs[$key]->imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->badgeid, '/', 'f1', false);
            $durl = new moodle_url("/local/badgecerts/mycerts.php", ['donwload' => $badge->id, 'hash' => $badge->uniquehash]);
            $durl = new moodle_url('/local/badgecerts/mycerts.php',
                        array('download' => $badge->id, 'hash' => $badge->uniquehash, 'sesskey' => sesskey()));
            $certs[$key]->downloadurl = html_entity_decode($durl->out());
        }

        $data = array('badges' => array_values($certs));

        return array(
            'templates' => array(
                array(
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_badgecerts/mobile_badgecerts_list', $data)
                )
            ),
            'javascript' => "",
            'otherdata' => ''

        );
    }

    /**
     * Returno certificate file for downloading.
     * @param  array $args Arguments from tool_mobile_get_content WS
     *
     * @return array       HTML, javascript and otherdata
     */
    public static function mobile_badgecerts_download($args) {
        global $OUTPUT, $USER, $CFG;

        $args = (object) $args;
        $uid = uniqid();

        $url = new moodle_url('/local/badgecerts/getfile.php', ['hash' => $args->hash, 'certid' => $args->certid, 'userid' => $USER->id, 'filename' => "{$uid}.pdf"]);

        $data = array(
            'data' => $url,
            'filename' => $uid . ".pdf",
            'fileurl' => $url,
            'url' => $url
        );

        return array(
            'templates' => array(
                array(
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_badgecerts/mobile_badgecerts_download', $data)
                )
            ),
            'javascript' => "",
            'otherdata' => ''

        );
    }
}