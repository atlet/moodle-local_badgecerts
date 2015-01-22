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
 * Editing badge certificate details
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/filestorage/file_storage.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');
require_once($CFG->dirroot . '/local/badgecerts/edit_form.php');

$certid = required_param('id', PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$cert = new badge_certificate($certid);
$context = $cert->get_context();
$navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type));

if (!((has_capability('local/badgecerts:configurecertificate', $context) && $cert->official == '0') || (has_any_capability(array('moodle/role:manage'), $context)))) {
    redirect(new moodle_url('/local/badgecerts/overview.php', array('id' => $certid)));
}

if ($cert->type == CERT_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        print_error('coursebadgesdisabled', 'badges');
    }
    require_login($cert->courseid);
    $navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type, 'id' => $cert->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    navigation_node::override_active_url($navurl, true);
}

$currenturl = new moodle_url('/local/badgecerts/edit.php', array('id' => $cert->id));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($cert->name);
$PAGE->set_title($cert->name);
$PAGE->navbar->add($cert->name);

$output = $PAGE->get_renderer('local_badgecerts');
$statusmsg = '';
$errormsg  = '';

$form_class = 'edit_cert_details_form';
$form = new $form_class($currenturl, array('badgecertificate' => $cert, 'action' => 'details'));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/badgecerts/overview.php', array('id' => $certid)));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    $getfilename = $form->get_new_filename('certbgimage');
    $cert->name = $data->name;
    $cert->description = $data->description;
    $existingbgimage = $DB->get_field('badge_certificate', 'certbgimage', array('id' => $cert->id));
    $cert->official = isset($data->official) ? $data->official : 0;
    $cert->usermodified = $USER->id;
    $cert->issuername = $data->issuername;
    $cert->issuercontact = $data->issuercontact;
    $cert->format = $data->format;
    $cert->orientation = $data->orientation;
    $cert->unit = $data->unit;
    $cert->bookingid = $data->bookingid;
    $cert->certtype = $data->certtype;

    if ($cert->save()) {
        if ($getfilename) {
            // Create folder if it doesn't exist.
            $dirname = $CFG->dataroot.'/filedir/cert';
            if (!file_exists($dirname) and !is_dir($dirname)) {
                mkdir($dirname);         
            }
            $filename = $dirname . '/' . $cert->id . '_' . $getfilename;
            // Save file to standard filesystem.
            $form->save_file('certbgimage', $filename, true);
            // Update record in the database.
            $DB->set_field('badge_certificate', 'certbgimage', $filename, array('id' => $cert->id));
        }

        $form->set_data($cert);
        $statusmsg = get_string('changessaved');
    } else {
        $errormsg = get_string('error:savecert', 'badges');
    }
    redirect(new moodle_url('/local/badgecerts/overview.php', array('id' => $certid)));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($cert->name);

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

echo $output->print_badgecert_status_box($cert);
$output->print_badgecert_tabs($certid, $context, 'details');

$form->display();

echo $OUTPUT->footer();