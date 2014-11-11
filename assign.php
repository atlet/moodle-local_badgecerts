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
 * Assign badge certificate to a single/multiple badge(s)
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');


if ($_POST) {
    $form_vars = $_POST;
}
elseif ($_GET) {
    $form_vars = $_GET;
}
if ($form_vars) {
    // Assign current badge cetificate to one or more badge(s)
    if (isset($form_vars['assign'])) {
        $assignids = $form_vars['available'];
        foreach ($assignids as $assignid) {
            $certid = $form_vars['certid'];
            $courseid = $form_vars['courseid'];
            $record = new StdClass();
            $record->id = $assignid;
            $record->certid = $certid;
            $record->courseid = $courseid;
            $DB->update_record('badge', $record);
        }
        redirect('assign.php?id='.$certid);
    }
    // Unassign current badge cetificate to one or more badge(s)
    if (isset($form_vars['remove'])) {
        $removeids = $form_vars['assigned'];
        foreach ($removeids as $removeid) {
            $certid = $form_vars['certid'];
            $record = new StdClass();
            $record->id = $removeid;
            $record->certid = null;
            $DB->update_record('badge', $record);
        }
        redirect('assign.php?id='.$certid);
    }
}

$certid = required_param('id', PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$cert = new badge_certificate($certid);
$context = $cert->get_context();
$navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type));

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

$currenturl = new moodle_url('/local/badgecerts/assign.php', array('id' => $cert->id));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($cert->name);
$PAGE->set_title($cert->name);
$PAGE->navbar->add($cert->name);

echo $OUTPUT->header();
echo $OUTPUT->heading($cert->name);

$output = $PAGE->get_renderer('local_badgecerts');
echo $output->print_badgecert_status_box($cert);
$output->print_badgecert_tabs($certid, $context, 'assign');


echo '<form id="assignbadgeform" action="assign.php" method="post">'."\n";
echo '<div>'."\n";
echo '<input type="hidden" name="certid" value="' . $cert->id . '" />'."\n";
echo '<input type="hidden" name="courseid" value="' . $cert->courseid . '" />'."\n";
echo '<table cellpadding="6" class="generaltable generalbox boxaligncenter" summary="">'."\n";
echo '<tr>'."\n";

// Output badge certificate assigned badges
if (isset($cert->courseid)) {
    $assigned = get_assigned_badge_options($cert->courseid, $cert->id);
} else {
    $assigned = array();
}

echo "<td>\n";
echo '<p><b><label for="assigned">'.get_string('assignedbadges', 'local_badgecerts').'</label></b></p>'."\n";
echo '<select name="assigned[]" multiple="multiple" id="assigned" size="15" class="select"'."\n";
echo ' onclick="window.status=this.selectedIndex==-1 ? \'\' : this.options[this.selectedIndex].title;" onmouseout="window.status=\'\';">'."\n";
if ($assigned) {
    // Print out the HTML
    foreach ($assigned as $key => $value) {
        echo "<option value=\"$key\" title=\"$value\">$value</option>\n";
    }
} else {
    // Print an empty option to avoid the XHTML error of having an empty select element
    echo '<option>&nbsp;</option>';
}
echo '</select>'."\n";
echo '</td>'."\n";

echo '<td id="buttonscell">'."\n";
echo '<p class="arrow_button">'."\n";
echo '<input name="assign" id="assign" value="'.get_string('assignl', 'local_badgecerts').'" title="'.get_string('assign', 'local_badgecerts').'" type="submit">'."\n";
echo '<br></br>'."\n";
echo '<input name="remove" id="remove" value="'.get_string('remover', 'local_badgecerts').'" title="'.get_string('remove', 'local_badgecerts').'" type="submit">'."\n";
echo '</p>'."\n";
echo '</td>'."\n";

// Output available badges
if (isset($cert->courseid)) {
    $available = get_available_badge_options($cert->courseid);
} else {
    $available = array();
}

echo "<td>\n";
echo '<p><b><label for="available">'.get_string('availablebadges', 'local_badgecerts').'</label></b></p>'."\n";
echo '<select name="available[]" multiple="multiple" id="available" size="15" class="select"'."\n";
echo ' onclick="window.status=this.selectedIndex==-1 ? \'\' : this.options[this.selectedIndex].title;" onmouseout="window.status=\'\';">'."\n";
if ($available) {
    // Print out the HTML
    foreach ($available as $key => $value) {
        echo "<option value=\"$key\" title=\"$value\">$value</option>\n";
    }
} else {
    // Print an empty option to avoid the XHTML error of having an empty select element
    echo '<option>&nbsp;</option>';
}
echo '</select>'."\n";
echo '</td>'."\n";

echo '</tr>'."\n";
echo '</table>'."\n";
echo '</div>'."\n";
echo '</form>'."\n";


echo $OUTPUT->footer();
