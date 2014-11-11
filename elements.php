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
 * Editing badge certificate elements
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

$certid  = required_param('id', PARAM_INT);
$update  = optional_param('update', 0, PARAM_INT);
$page    = optional_param('page', 0, PARAM_INT);
$sorthow = optional_param('dir', 'ASC', PARAM_ALPHA);
$action  = optional_param('action', '', PARAM_ALPHA);
$edit    = optional_param('edit', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$cert = new badge_certificate($certid);
$context = $cert->get_context();
$navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type));

require_capability('moodle/badges:configureelements', $context);

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

$currenturl = new moodle_url('/local/badgecerts/elements.php', array('id' => $certid));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($cert->name);
$PAGE->set_title($cert->name);
$PAGE->navbar->add($cert->name);

$output = $PAGE->get_renderer('local_badgecerts');

if (($action) && has_capability('moodle/badges:configureelements', $PAGE->context)) {
    $fordb = new stdClass();
    $fordb->id = null;

    $form = new edit_cert_element_form($PAGE->url, array('action' => 'new'));

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/local/badgecerts/elements.php', array('id' => $certid)));
    } else if ($data = $form->get_data()) {
        // Creating new badge certificate element here.
        $fordb->certid = $certid;
        $fordb->x = $data->positionx;
        $fordb->y = $data->positiony;
        $fordb->size = $data->size;
        $fordb->family = $data->family;
        $fordb->rawtext = $data->rawtext;
        $fordb->align = $data->align;

        $newid = $DB->insert_record('badge_certificate_elms', $fordb, true);

        redirect(new moodle_url('/local/badgecerts/elements.php', array('id' => $certid)));
    }
}

if (($delete) && has_capability('moodle/badges:configureelements', $PAGE->context)) {
    $DB->delete_records('badge_certificate_elms', array('id' => $delete));
    redirect($currenturl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($cert->name);

echo $output->print_badgecert_status_box($cert);
$output->print_badgecert_tabs($certid, $context, 'elements');

$totalcount = count(badges_get_certelements($certid, '', '', '', ''));
$records = badges_get_certelements($certid, '', $sorthow, $page, CERT_PERPAGE);

if ($totalcount) {
    echo $output->heading(get_string('elementscontained', 'local_badgecerts', $totalcount), 4);

    $elements             = new cert_element_management($records);
    $elements->dir        = $sorthow;
    $elements->page       = $page;
    $elements->perpage    = CERT_PERPAGE;
    $elements->totalcount = $totalcount;

    echo $output->render($elements);
} else {
    echo $output->notification(get_string('nobadgecertificateelms', 'local_badgecerts'));

    if (has_capability('moodle/badges:createcertificate', $PAGE->context)) {
        echo $OUTPUT->single_button(new moodle_url('newelement.php', array('id' => $certid)),
            get_string('newelement', 'local_badgecerts'));
    }
}

echo $OUTPUT->footer();