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
 * Recipients overview page
 *
 * @package    local_badgecerts
 * @copyright  2015 onwards Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletekj@gmail.com>
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once "$CFG->libdir/tablelib.php";
require_once($CFG->dirroot . '/local/badgecerts/classes/all_users.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

$certid = required_param('id', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$day = optional_param('day', 0, PARAM_INT);
$month = optional_param('month', 0, PARAM_INT);
$year = optional_param('year', 0, PARAM_INT);

$sqlWhere = '';
$sqlValues = array();
$urlParams = array();
$sqlValues['certid'] = $certid;
$urlParams['id'] = $certid;

if ($day > 0) {
    $sqlValues['day'] = $day;
    $urlParams['day'] = $day;
    $sqlWhere .= " AND FROM_UNIXTIME(d.dateissued, '%e') = :day ";
}

if ($month > 0) {
    $sqlValues['month'] = $month;
    $urlParams['month'] = $month;
    $sqlWhere .= " AND FROM_UNIXTIME(d.dateissued, '%c') = :month ";
}

if ($year > 0) {
    $sqlValues['year'] = $year;
    $urlParams['year'] = $year;
    $sqlWhere .= " AND FROM_UNIXTIME(d.dateissued, '%Y') = :year ";
}

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$cert = new badge_certificate($certid);
$context = $cert->get_context();
require_capability('moodle/badges:viewcertificates', $context);
$navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type));

$onlyTeachers = "";
if ($cert->bookingid > 0 && !has_capability('moodle/badges:certificatemanager', $context)) {
    $onlyTeachers = " JOIN {booking_answers} AS ba ON ba.userid = u.id JOIN {booking_teachers} AS bt ON bt.optionid = ba.optionid ";
    
    $sqlWhere .= " AND bt.userid = :teacherid ";
    $sqlValues['teacherid'] = $USER->id;
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

$currenturl = new moodle_url('/local/badgecerts/view.php', $urlParams);

$table = new all_users('all_users');
$table->is_downloading($download, 'all_users', 'testing123');

$fields = 'DISTINCT u.id, ' . get_all_user_name_fields(true, 'u') . ', u.username, u.firstname, u.lastname, d.dateissued, d.uniquehash, '
        . '(SELECT COUNT(*) AS nctransfers FROM {badge_certificate_trasnfers} AS bcf WHERE bcf.userid = u.id AND bcf.badgecertificateid = c.id AND bcf.transfereruserid = u.id) AS nctransfers,'
        . '(SELECT created AS ndatelasttransfer FROM {badge_certificate_trasnfers} AS bcf WHERE bcf.userid = u.id AND bcf.badgecertificateid = c.id AND bcf.transfereruserid = u.id ORDER BY created DESC LIMIT 1) AS ndatelasttransfer';
$from = '{badge_issued} AS d JOIN {badge} AS b ON d.badgeid = b.id JOIN {user} AS u ON d.userid = u.id JOIN {badge_certificate} AS c ON b.certid = c.id' . $onlyTeachers;

$where = 'b.certid = :certid
            ' . $sqlWhere . '
        AND (SELECT 
            IF(c.bookingid > 0,
                    (SELECT 
                            IF(COUNT(*) > 0, 1, 0)
                        FROM
                            mdl_booking_answers AS ans
                        WHERE
                            bookingid = (SELECT 
                                    instance
                                FROM
                                    mdl_course_modules AS cm
                                WHERE
                                    cm.id = c.bookingid)
                                AND userid = u.id),
                    1)
        ) = 1';

$table->set_sql(
        $fields, $from, $where, $sqlValues);

$table->define_baseurl($currenturl);
$table->is_downloadable(false);
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $badges = array();

    if (isset($_POST['printselected']) && isset($_POST['user'])) {
        foreach ($_POST['user'] as $key => $value) {

            $user = new stdClass();

            $user->userid = key($value);
            $user->hash = current($value);

            $badges[key($value)] = $user;
        }

        bulk_generate_certificates($certid, $badges, $context);

        die();
    }

    if (isset($_POST['printall'])) {
        
        $users = $DB->get_records_sql("SELECT {$fields} FROM {$from} WHERE {$where}", $sqlValues);
        $badges = array(); 
        
        foreach ($users as $value) {

            $user = new stdClass();

            $user->userid = $value->id;
            $user->hash = $value->uniquehash;

            $badges[$value->id] = $user;
        }
        
        if (empty($badges)) {
            print_error('nousers', 'local_badgecerts');
        } else {        
            bulk_generate_certificates($certid, $badges, $context);
        }

        die();
    }
}

if (!$table->is_downloading()) {
    $PAGE->set_context($context);
    $PAGE->set_url($currenturl);
    $PAGE->set_heading($cert->name);
    $PAGE->set_title($cert->name);
    $PAGE->navbar->add($cert->name);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($cert->name);

    $output = $PAGE->get_renderer('local_badgecerts');
    echo $output->print_badgecert_status_box($cert);
    $output->print_badgecert_tabs($certid, $context, 'view');
    echo $output->print_badgecert_view($cert, $context);

    $output->print_badgecert_filter_box($cert, $currenturl, $day, $month, $year);

    echo '<form action="' . $currenturl . '" method="post" id="studentsform">' . "\n";
    echo '<div>' . "\n";
}

$table->out(25, true);

if (!$table->is_downloading()) {
    if (has_capability('moodle/badges:printcertificates', $context)) {
        echo '<div class="selectbuttons">';
        echo '<input type="hidden" name="id" value="' . $certid . '" />';
        echo '<input type="button" id="checkall" value="' . get_string('selectall') . '" /> ';
        echo '<input type="button" id="checknone" value="' . get_string('deselectall') . '" /> ';

        echo '</div>';
        echo '<div>';
        echo '<input type="submit" name="printselected" value="' . get_string('printselected', 'local_badgecerts') . '" />';
        echo '<input type="submit" name="printall" value="' . get_string('printall', 'local_badgecerts') . '" />';
        echo '</div>';
        echo '</div>';
        echo '</form>';


        $PAGE->requires->js_init_call('M.local_badgecerts.init');
    }

    echo $OUTPUT->footer();
}
