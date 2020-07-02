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
 * Editing badge certificate details.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');
require_once($CFG->dirroot . '/local/badgecerts/editelement_form.php');

$elementid = required_param('id', PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$element = new badge_cert_element($elementid);
$context = $element->get_context();

require_capability('local/badgecerts:configuredetails', $context);

if ($element->certtype == CERT_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        print_error('coursebadgesdisabled', 'badges');
    }
    require_login($element->courseid);
    $navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $element->certtype, 'id' => $element->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    navigation_node::override_active_url($navurl, true);
}

$currenturl = new moodle_url('/local/badgecerts/editelement.php', array('id' => $element->id));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($element->certname);
$PAGE->set_title($element->certname);
$PAGE->navbar->add($element->certname);

$output = $PAGE->get_renderer('local_badgecerts');
$statusmsg = '';
$errormsg  = '';

$form = new edit_cert_element_form($currenturl, array('certificateelement' => $element, 'action' => 'details'));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/badgecerts/elements.php', array('id' => $element->certid)));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    // Updating badge certificate element here.
    $element->x = $data->x;
    $element->y = $data->y;
    $element->rawtext = $data->rawtext;
    $element->size = $data->size;
    $element->family = $data->family;
    $element->align = $data->align;

    if ($element->save()) {
        $form->set_data($element);
        $statusmsg = get_string('changessaved');
    } else {
        $errormsg = get_string('error:savecert', 'local_badgecerts');
    }
    redirect(new moodle_url('/local/badgecerts/elements.php', array('id' => $element->certid)));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($element->certname);

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

$output->print_badgecert_tabs($element->certid, $context, 'elements');

$form->display();

echo $OUTPUT->footer();