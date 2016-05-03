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
 * Page for badge certificates management
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');


$type       = required_param('type', PARAM_INT);
$courseid   = optional_param('id', 0, PARAM_INT);
$page       = optional_param('page', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$sortby     = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'ASC', PARAM_ALPHA);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$delete     = optional_param('delete', 0, PARAM_INT);
$msg        = optional_param('msg', '', PARAM_TEXT);

if (!in_array($sortby, array('name', 'status'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'ASC';
}

if ($page < 0) {
    $page = 0;
}

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

if (empty($CFG->badges_allowcoursebadges) && ($type == CERT_TYPE_COURSE)) {
    print_error('coursebadgesdisabled', 'badges');
}

$err = '';
$urlparams = array('sort' => $sortby, 'dir' => $sorthow, 'page' => $page);

if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $urlparams['type'] = $type;
    $urlparams['id'] = $course->id;
} else {
    $urlparams['type'] = $type;
}

$hdr = get_string('managebadgecertificates', 'local_badgecerts');
$returnurl = new moodle_url('/local/badgecerts/index.php', $urlparams);
$PAGE->set_url($returnurl);

if ($type == CERT_TYPE_SITE) {
    $title = get_string('sitebadgecertificates', 'local_badgecerts');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading($title . ': ' . $hdr);
    navigation_node::override_active_url(new moodle_url('/local/badgecerts/index.php', array('type' => CERT_TYPE_SITE)), true);
} else {
    require_login($course);
    $coursecontext = context_course::instance($course->id);
    $title = get_string('coursebadgecertificates', 'local_badgecerts');
    $PAGE->set_context($coursecontext);
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading(format_string($course->fullname, true, array('context' => $coursecontext)) . ': ' . $hdr);
    navigation_node::override_active_url(
        new moodle_url('/moodle/badges/index.php', array('type' => CERT_TYPE_COURSE, 'id' => $course->id))
    );
}

if (!has_any_capability(array(
        'local/badgecerts:viewcertificates',
        'local/badgecerts:createcertificate',
        'local/badgecerts:configurecertificate',
        'local/badgecerts:configureelements',
        'local/badgecerts:deletecertificate'), $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($hdr);
$output = $PAGE->get_renderer('local_badgecerts');

if (($delete) && has_capability('local/badgecerts:deletecertificate', $PAGE->context)) {
    $certid = $delete;
    $cert = new badge_certificate($certid);
    if (!$confirm) {
        echo $output->header();
        // Delete this badge certificate?
        echo $output->heading(get_string('delbadgecertificate', 'local_badgecerts', $cert->name));
        $deletebutton = $output->single_button(
                            new moodle_url($PAGE->url, array('delete' => $cert->id, 'confirm' => 1)),
                            get_string('delconfirmcert', 'local_badgecerts'));
        echo $output->box(get_string('deletehelpcert', 'local_badgecerts') . $deletebutton, 'generalbox');

        // Go back.
        echo $output->action_link($returnurl, get_string('cancel'));

        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $cert->delete();
        redirect($returnurl);
    }
}

if ($deactivate && has_capability('local/badgecerts:configurecertificate', $PAGE->context)) {
    require_sesskey();
    $cert = new badge_certificate($deactivate);
    if ($cert->is_locked()) {
        $cert->set_status(CERT_STATUS_INACTIVE_LOCKED);
    } else {
        $cert->set_status(CERT_STATUS_INACTIVE);
    }
    $msg = 'deactivatesuccess';
    $returnurl->param('msg', $msg);
    redirect($returnurl);
}

echo $OUTPUT->header();
if ($type == CERT_TYPE_SITE) {
    echo $OUTPUT->heading_with_help($PAGE->heading, 'sitebadgecertificates', 'local_badgecerts');
} else {
    echo $OUTPUT->heading($PAGE->heading);
}
echo $OUTPUT->box('', 'notifyproblem hide', 'check_connection');

$totalcount = count(badges_get_certificates($type, $courseid, '', '' , '', '', $USER->id));
$records = badges_get_certificates($type, $courseid, $sortby, $sorthow, $page, CERT_PERPAGE, $USER->id);

if ($totalcount) {
    echo $output->heading(get_string('certificatesavailable', 'local_badgecerts', $totalcount), 4);

    if ($course && $course->startdate > time()) {
        echo $OUTPUT->box(get_string('error:notifycoursedate', 'badges'), 'generalbox notifyproblem');
    }

    if ($err !== '') {
        echo $OUTPUT->notification($err, 'notifyproblem');
    }

    if ($msg !== '') {
        echo $OUTPUT->notification(get_string($msg, 'badges'), 'notifysuccess');
    }

    $certs             = new cert_management($records);
    $certs->sort       = $sortby;
    $certs->dir        = $sorthow;
    $certs->page       = $page;
    $certs->perpage    = CERT_PERPAGE;
    $certs->totalcount = $totalcount;

    echo $output->render($certs);
} else {
    echo $output->notification(get_string('nobadgecertificates', 'local_badgecerts'));

    if (has_capability('local/badgecerts:createcertificate', $PAGE->context)) {
        echo $OUTPUT->single_button(new moodle_url('/local/badgecerts/new.php', array('type' => $type, 'id' => $courseid)),
            get_string('newbadgecertificate', 'local_badgecerts'));
    }
}

echo $OUTPUT->footer();