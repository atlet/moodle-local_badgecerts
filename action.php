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
 * Page to handle actions associated with badge certificate management.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

$certid = required_param('id', PARAM_INT);
$copy = optional_param('copy', 0, PARAM_BOOL);
$preview = optional_param('preview', 0, PARAM_BOOL);
$download = optional_param('download', 0, PARAM_BOOL);
$activate = optional_param('activate', 0, PARAM_BOOL);
$deactivate = optional_param('lock', 0, PARAM_BOOL);
$confirm   = optional_param('confirm', 0, PARAM_BOOL);
$return = optional_param('return', 0, PARAM_LOCALURL);

require_login();

$cert = new badge_certificate($certid);
$context = $cert->get_context();
$navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type));

if ($cert->type == CERT_TYPE_COURSE) {
    require_login($cert->courseid);
    $navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type, 'id' => $cert->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    navigation_node::override_active_url($navurl, true);
}

$PAGE->set_context($context);
$PAGE->set_url('/local/badgecerts/action.php', array('id' => $cert->id));

if ($return !== 0) {
    $returnurl = new moodle_url($return);
} else {
    $returnurl = new moodle_url('/local/badgecerts/overview.php', array('id' => $cert->id));
}
$returnurl->remove_params('awards'); // ???

if ($copy) {
    require_sesskey();
    require_capability('moodle/badges:createcertificate', $context);

    $cloneid = $cert->make_clone();
    // If a user can edit badge certificate details, they will be redirected to the edit page.
    if (has_capability('moodle/badges:configurecertificate', $context)) {
        redirect(new moodle_url('/local/badgecerts/edit.php', array('id' => $cloneid)));
    }
    redirect(new moodle_url('/local/badgecerts/overview.php', array('id' => $cloneid)));
}

if ($preview) {
    //require_sesskey();
    require_capability('moodle/badges:createcertificate', $context);
    $cert->preview_badge_certificate();
}

if ($download) {
    //require_sesskey();
    require_capability('moodle/badges:configurecertificate', $context);
    bulk_generate_badge_certificates($cert->courseid, $cert->id);
}

if ($activate) {
    require_capability('moodle/badges:configurecertificate', $context);

    $PAGE->url->param('activate', 1);
    $status = ($cert->status == CERT_STATUS_INACTIVE) ? CERT_STATUS_ACTIVE : CERT_STATUS_ACTIVE_LOCKED;
    $cert->set_status($status);
    redirect($returnurl);

    $strheading = get_string('reviewbadgecertificate', 'badges');
    $PAGE->navbar->add($strheading);
    $PAGE->set_title($strheading);
    $PAGE->set_heading($cert->name);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strheading);

    $params = array('id' => $cert->id, 'activate' => 1, 'sesskey' => sesskey(), 'confirm' => 1, 'return' => $return);
    $url = new moodle_url('/local/badgecerts/action.php', $params);

    if (!$cert->has_elements()) {
        echo $OUTPUT->notification(get_string('error:cannotactcert', 'badges') . get_string('noelements', 'badges'));
        echo $OUTPUT->continue_button($returnurl);
    } else {
        $message = get_string('reviewcertconfirm', 'badges', $cert->name);
        echo $OUTPUT->confirm($message, $url, $returnurl);
    }
    echo $OUTPUT->footer();
    die;
}

if ($deactivate) {
    require_sesskey();
    require_capability('moodle/badges:configurecriteria', $context);

    $status = ($cert->status == CERT_STATUS_ACTIVE) ? CERT_STATUS_INACTIVE : CERT_STATUS_INACTIVE_LOCKED;
    $cert->set_status($status);
    redirect($returnurl);
}
