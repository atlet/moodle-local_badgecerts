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
 * Displays user badges for badges management in own profile.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');
require_once($CFG->dirroot . '/local/badgecerts/renderer.php');

$page = optional_param('page', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_CLEAN);
$clearsearch = optional_param('clearsearch', '', PARAM_TEXT);
$download = optional_param('download', 0, PARAM_INT);
$hash = optional_param('hash', '', PARAM_ALPHANUM);

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$url = new moodle_url('/local/badgecerts/mycerts.php');
$PAGE->set_url($url);

require_login();

if (isguestuser()) {
    $PAGE->set_context(context_system::instance());
    echo $OUTPUT->header();
    echo $OUTPUT->box(get_string('error:guestuseraccess', 'badges'), 'notifyproblem');
    echo $OUTPUT->footer();
    die();
}

if ($page < 0) {
    $page = 0;
}

if ($clearsearch) {
    $search = '';
}

$context = context_user::instance($USER->id);

$PAGE->set_context($context);

if ($download && $hash) {
    $badges = array();

    $user = new stdClass();

    $user->userid = $USER->id;
    $user->hash = $hash;

    $badges[$USER->id] = $user;

    bulk_generate_certificates($download, $badges);
}

$title = get_string('mybadgecertificates', 'local_badgecerts');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');

$output = $PAGE->get_renderer('local_badgecerts');

echo $OUTPUT->header();

$records = badges_get_user_certificates($USER->id, null, $page, CERT_PERPAGE, $search);
$totalcount = count($records);

$usercerts = new cert_user_collection($records, $USER->id);
$usercerts->sort = 'dateissued';
$usercerts->dir = 'DESC';
$usercerts->page = $page;
$usercerts->perpage = CERT_PERPAGE;
$usercerts->totalcount = $totalcount;
$usercerts->search = $search;

echo $output->render($usercerts);

echo $OUTPUT->footer();
