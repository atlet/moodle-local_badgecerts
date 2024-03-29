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
require_once("$CFG->libdir/tablelib.php");
require_once($CFG->dirroot . '/local/badgecerts/classes/all_users.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

$certid = required_param('id', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$day = optional_param('day', 0, PARAM_INT);
$month = optional_param('month', 0, PARAM_INT);
$year = optional_param('year', 0, PARAM_INT);

$dayend = optional_param('dayend', 0, PARAM_INT);
$monthend = optional_param('monthend', 0, PARAM_INT);
$yearend = optional_param('yearend', 0, PARAM_INT);

$sqlwhere = '';
$sqlvalues = array();
$urlparams = array();
$sqlvalues['certid'] = $certid;
$urlparams['id'] = $certid;

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$cert = new badge_certificate($certid);

if ($day > 0) {
    $urlparams['day'] = $day;
    $urlparams['month'] = $month;
    $urlparams['year'] = $year;
    $urlparams['dayend'] = $dayend;
    $urlparams['monthend'] = $monthend;
    $urlparams['yearend'] = $yearend;

    // Badge - 0
    // Booking users - 1
    // Booking teachers - 2
    // Quiz grading - 3
    // Booking users - SUM - 4.

    if (in_array($cert->certtype, array(0, 1, 2, 4))) {
        $sqlwhere .= " AND d.dateissued BETWEEN :startdate AND :enddate";
    } else {
        $sqlwhere .= " AND (SELECT COUNT(*) FROM {quizgrading_results} qr WHERE qr.userid = u.id AND qr.quizgradingid = c.quizgradingid AND
        qr.datum_resitve BETWEEN :startdate AND :enddate) > 0 ";
    }

    $sqlvalues['startdate'] = mktime(0, 0, 0, $month, $day, $year);
    $sqlvalues['enddate'] = mktime(23, 59, 59, $monthend, $dayend, $yearend);
}

$context = $cert->get_context();
require_capability('local/badgecerts:viewcertificates', $context);
$navurl = new moodle_url('/local/badgecerts/index.php', array('type' => $cert->type));

$onlyteachers = "";
if (in_array($cert->certtype, array(1, 2, 4)) && $cert->bookingid > 0 &&
    !has_capability('local/badgecerts:certificatemanager', $context)) {
    if (has_capability('local/badgecerts:certificatemanagerowninstitution', $context)) {
        $userobj = $DB->get_record('user', array('id' => $USER->id));
        $sqlwhere .= ' AND u.institution = :institution ';
        $sqlvalues['institution'] = $userobj->institution;
    } else {
        $onlyteachers = " JOIN {booking_answers} bat ON bat.userid = u.id JOIN " .
            "{booking_teachers} bta ON bta.optionid = bat.optionid ";

        $sqlwhere .= ' AND bta.userid = :teacherid ';
        $sqlvalues['teacherid'] = $USER->id;
    }
}

if (in_array($cert->certtype, array(3)) && !has_capability('local/badgecerts:certificatemanager', $context)) {
    $sqlwhere .= ' AND u.id IN (SELECT userid FROM {quizgrading_results} WHERE mentorid = :teacherid) ';
    $sqlvalues['teacherid'] = $USER->id;
}

switch ($cert->certtype) {
    case 1:
    case 4:
        // Mod_booking users.
        if ($cert->bookingid > 0) {
            if ($cert->enablebookingoptions) {
                $yesno = ($cert->optionsincexc == 1 ? '' : 'NOT');
                $exsql = " AND ans.optionid {$yesno} IN ({$cert->bookingoptions})";
            } else {
                $exsql = '';
            }
            if ($cert->startdate != 0) {
                $sqlwhere .= " AND (SELECT
                            CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                        FROM
                            {booking_answers} ans
                        LEFT JOIN
                            {booking_options} bo ON bo.id = ans.optionid
                        WHERE
                            ans.bookingid = (SELECT
                                    instance
                                FROM
                                    {course_modules} cm
                                WHERE
                                    cm.id = c.bookingid)
                                AND ans.userid = u.id AND ans.completed = 1 AND " .
                                "bo.coursestarttime >= c.startdate AND bo.courseendtime <= c.enddate {$exsql}) = 1";
            } else {
                $sqlwhere .= " AND (SELECT
                            CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                        FROM
                            {booking_answers} ans
                        WHERE
                            bookingid = (SELECT
                                    instance
                                FROM
                                    {course_modules} cm
                                WHERE
                                    cm.id = c.bookingid)
                                AND ans.userid = u.id AND ans.completed = 1 {$exsql}) = 1";
            }
        }
        break;

    case 2:
        // Mod_booking teachers.
        if ($cert->bookingid > 0) {
            if ($cert->enablebookingoptions) {
                $yesno = ($cert->optionsincexc == 1 ? '' : 'NOT');
                $exsql = " AND tch.optionid {$yesno} IN ({$cert->bookingoptions})";
            } else {
                $exsql = '';
            }
            if ($cert->startdate != 0) {
                $sqlwhere .= " AND (SELECT
                            CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                        FROM
                            {booking_teachers} tch
                        LEFT JOIN
                            {booking_options} bo ON bo.id = tch.optionid
                        WHERE
                            tch.bookingid = (SELECT
                                    instance
                                FROM
                                    {course_modules} cm
                                WHERE
                                    cm.id = c.bookingid)
                                AND tch.userid = u.id AND tch.completed = 1 AND
                                bo.coursestarttime >= c.startdate AND
                                bo.courseendtime <= c.enddate {$exsql}) = 1 ";
            } else {
                $sqlwhere .= " AND (SELECT
            CASE WHEN c.bookingid > 0 THEN
                    (SELECT
                        CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                        FROM
                            {booking_teachers} tch
                        WHERE
                            bookingid = (SELECT
                                    instance
                                FROM
                                    {course_modules} cm
                                WHERE
                                    cm.id = c.bookingid)
                                AND tch.userid = u.id AND tch.completed = 1 {$exsql})ELSE 0 END ) = 1 ";
            }
        }
        break;

    case 3:
        // Mod_quizgrading.
        if ($cert->quizgradingid > 0) {
            $sqlwhere .= " AND c.quizgradingid = :quizgradingid ";
            $sqlvalues['quizgradingid'] = $cert->quizgradingid;
        }
        break;

    default:
        break;
}

if (in_array($cert->certtype, array(1, 2, 4)) && $cert->startdate != 0) {
    $sqlwhere .= " ";
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

$currenturl = new moodle_url('/local/badgecerts/view.php', $urlparams);

$table = new all_users('all_users_bcde');
$table->is_downloading($download, 'all_users_bcde', 'testing123456');

if ($CFG->version >= 2021051700) {
    // This only works in Moodle 3.11 and later.
    $namefields = \core_user\fields::for_name()->get_sql('u')->selects;
    $namefields = trim($namefields, ', ');
} else {
    // This is only here to support Moodle versions earlier than 3.11.
    $namefields = get_all_user_name_fields(true, 'u');
}

$fields = 'DISTINCT u.id, ' . $namefields . ', u.username, d.dateissued, d.uniquehash, '
        . '(SELECT COUNT(*) nctransfers FROM {local_badgecerts_trasnfers} bcf WHERE bcf.userid = u.id AND '
        . 'bcf.badgecertificateid = c.id AND bcf.transfereruserid = u.id) nctransfers,'
        . '(SELECT COUNT(*) nctransfers FROM {local_badgecerts_trasnfers} bcf WHERE '
        . 'bcf.userid = u.id AND bcf.badgecertificateid = c.id AND bcf.transfereruserid = '
        . $USER->id . ') nctransfersteacher,'
        . '(SELECT created ndatelasttransfer FROM {local_badgecerts_trasnfers} bcf WHERE bcf.userid = '
        . 'u.id AND bcf.badgecertificateid = c.id AND bcf.transfereruserid = u.id ORDER BY created DESC LIMIT 1) ndatelasttransfer';
$from = ' {badge_issued} d JOIN {badge} b ON d.badgeid = b.id JOIN {user} u ON d.userid = u.id JOIN '
        . '{local_badgecerts} c ON b.id = c.certid ' . $onlyteachers;

$where = ' c.id = :certid ' . $sqlwhere;

$table->set_count_sql("SELECT COUNT(*) FROM (SELECT {$fields} FROM {$from} WHERE {$where}) abcd WHERE 1=1", $sqlvalues);

$table->set_sql(
        $fields, $from, $where, $sqlvalues);

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

        bulk_generate_certificates($certid, $badges);

        die();
    }

    if (isset($_POST['printall'])) {

        $users = $DB->get_records_sql("SELECT {$fields} FROM {$from} WHERE {$where}", $sqlvalues);
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
            bulk_generate_certificates($certid, $badges);
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
    echo $output->print_badgecert_view();

    $output->print_badgecert_filter_box($cert, $currenturl, $day, $month, $year, $dayend, $monthend, $yearend);

    echo '<form action="' . $currenturl . '" method="post" id="studentsform">' . "\n";
    echo '<div>' . "\n";
}

$table->out(25, true);

if (!$table->is_downloading()) {
    if (has_capability('local/badgecerts:printcertificates', $context)) {
        echo '<div class="selectbuttons">';
        echo '<input type="hidden" name="id" value="' . $certid . '" />';
        echo '<input class="btn btn-secondary" type="button" id="checkall" value="' . get_string('selectall') . '" /> ';
        echo '<input class="btn btn-secondary" type="button" id="checknone" value="' . get_string('deselectall') . '" /> ';
        echo '<input class="btn btn-secondary" type="submit" name="printselected" value="'
            . get_string('printselected', 'local_badgecerts') . '" /> ';
        echo '<input class="btn btn-secondary" type="submit" name="printall" value="'
            . get_string('printall', 'local_badgecerts') . '" />';
        echo '</div>';
        echo '</div>';
        echo '</form>';


        $PAGE->requires->js_init_call('M.local_badgecerts.init');
    }

    echo $OUTPUT->footer();
}
?>

<script type="text/javascript">
    YUI().use('node-event-simulate', function (Y) {

        Y.one('#buttonclear').on('click', function () {
            window.location.href = '<?php echo new moodle_url('/local/badgecerts/view.php', array('id' => $certid)); ?>';
        });
    });
</script>