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
 * Recipients overview page.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');
require_once('migrate_form.php');

$certid = required_param('id', PARAM_INT);

$sqlwhere = '';
$sqlvalues = array();
$urlparams = array();
$sqlvalues['certid'] = $certid;
$urlparams['id'] = $certid;

require_login();

$cert = new badge_certificate($certid);

$context = $cert->get_context();
require_capability('local/badgecerts:viewcertificates', $context);
$navurl = new moodle_url('/local/badgecerts/migrate.php', array('id' => $cert->id));

// Set up the page.
$PAGE->set_url(new moodle_url('/local/badgecerts/migrate.php', array('id' => $cert->id)));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($cert->name);
$PAGE->set_heading($cert->name);

// Instantiate the form.
$form = new migrate_form($navurl, ['context' => $context]);

// Process form submission.
if ($form->is_submitted() && $form->is_validated()) {
    $data = $form->get_data();

    $task = new \local_badgecerts\task\adhoc_task();
    $task->set_custom_data(['id' => $cert->id, 'certificate' => $data->certificate]);
    \core\task\manager::queue_adhoc_task($task);

    redirect(new moodle_url('/local/badgecerts/view.php', ['id' => $cert->id]), get_string('migratesucesfull', 'local_badgecerts'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Output the page.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('migrate', 'local_badgecerts'));
$form->display();
echo $OUTPUT->footer();
