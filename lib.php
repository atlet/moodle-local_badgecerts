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
 * Contains classes, functions and constants used in 'local_badgecerts' plugin.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

use local_badgecerts\utils;

/*
 * Number of records per page.
 */
define('CERT_PERPAGE', 50);

/*
 * Inactive badge certificate means that this badge certificate cannot be used and
 * has not been used yet. Its elements can be changed.
 */
define('CERT_STATUS_INACTIVE', 0);

/*
 * Active badge certificate means that this badge certificate can be used, but it
 * has not been used yet. Can be deactivated for the purpose of changing its elements.
 */
define('CERT_STATUS_ACTIVE', 1);

/*
 * Inactive badge certificate can no longer be used, but it has been used in the past
 * and therefore its elements cannot be changed.
 */
define('CERT_STATUS_INACTIVE_LOCKED', 2);

/*
 * Active badge certificate means that it can be used and has already been used by
 * users. Its elements cannot be changed any more.
 */
define('CERT_STATUS_ACTIVE_LOCKED', 3);

/*
 * Badge certificate type for site badge certificates.
 */
define('CERT_TYPE_SITE', 1);

/*
 * Badge certificate type for course badge certificates.
 */
define('CERT_TYPE_COURSE', 2);

/**
 * Class that represents badge certificate.
 *
 */
class badge_certificate {

    /**
     * Instance id.
     *
     * @var integer
     */
    public $id;

    /**
     * Instance name.
     *
     * @var string
     */
    public $name;
    /**
     * Description.
     *
     * @var string
     */
    public $description;
    /**
     * SVG template.
     *
     * @var string
     */
    public $certbgimage;
    /**
     * Booking ID instance.
     *
     * @var integer
     */
    public $bookingid;
    /**
     * It's official?
     *
     * @var bool
     */
    public $official;
    /**
     * Created.
     *
     * @var timestamp
     */
    public $timecreated;
    /**
     * Last time that was modified.
     *
     * @var timestamp
     */
    public $timemodified;
    /**
     * User, that created this instance.
     *
     * @var int
     */
    public $usercreated;
    /**
     * User, that last modified this instance.
     *
     * @var int
     */
    public $usermodified;
    /**
     * Who issued this certificate.
     *
     * @var string
     */
    public $issuername;
    /**
     * Contact of issuer.
     *
     * @var string
     */
    public $issuercontact;
    /**
     * Format.
     *
     * @var string
     */
    public $format;
    /**
     * Orientatioin.
     *
     * @var string
     */
    public $orientation;
    /**
     * PDF unit.
     *
     * @var string
     */
    public $unit;
    /**
     * Type.
     *
     * @var string
     */
    public $type;
    /**
     * Course id.
     *
     * @var integer
     */
    public $courseid;
    /**
     * Status.
     *
     * @var integer
     */
    public $status = 0;
    /**
     * Next time cron need to run.
     *
     * @var timestamp
     */
    public $nextcron;
    /**
     * Type.
     *
     * @var integer
     */
    public $certtype;
    /**
     * Quizgrading instance.
     *
     * @var integer
     */
    public $quizgradingid;
    /**
     * Show qr code.
     *
     * @var bool
     */
    public $qrshow;
    /**
     * QR code position x.
     *
     * @var integer
     */
    public $qrx;
    /**
     * QR code position y.
     *
     * @var integer
     */
    public $qry;
    /**
     * QR code width.
     *
     * @var int
     */
    public $qrw;
    /**
     * QR code height.
     *
     * @var int
     */
    public $qrh;
    /**
     * QR code value.
     *
     * @var string
     */
    public $qrdata;
    /**
     * Start date.
     *
     * @var timestamp
     */
    public $startdate;
    /**
     * End date.
     *
     * @var timestamp.
     */
    public $enddate;
    /**
     * Certificate instance.
     *
     * @var int
     */
    public $certid;
    /**
     * Booking option filters.
     *
     * @var bool
     */
    public $enablebookingoptions;
    /**
     * Include or exclude.
     *
     * @var bool
     */
    public $optionsincexc;
    /**
     * Booking options to exlude.
     *
     * @var string
     */
    public $bookingoptions;

    /** @var array Badge certificate elements */
    public $elements = array();

    /**
     * Constructs with badge certificate details.
     *
     * @param int $certid badge certificate ID.
     */
    public function __construct($certid) {
        global $DB, $CFG;
        $this->id = $certid;

        $data = $DB->get_record('local_badgecerts', array('id' => $certid));

        if (empty($data)) {
            print_error('error:nosuchbadgecertificate', 'badges', $certid);
        }

        foreach ((array) $data as $field => $value) {
            if (property_exists($this, $field)) {
                if ($field == "certbgimage") {
                    $this->{$field} = $CFG->dataroot . $value;
                } else {
                    $this->{$field} = $value;
                }
            }
        }
    }

    /**
     * Use to get context instance of a badge certificate.
     * @return context instance.
     */
    public function get_context() {
        if ($this->type == CERT_TYPE_SITE) {
            return context_system::instance();
        } else if ($this->type == CERT_TYPE_COURSE) {
            return context_course::instance($this->courseid);
        } else {
            debugging('Something is wrong...');
        }
    }

    /**
     * Save/update badge certificate information in 'local_badgecerts'
     * table only. Cannot be used for updating badge certificate elements.
     *
     * @return bool Returns true on success.
     */
    public function save() {
        global $DB;
        $DB->set_debug(true);
        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }
        unset($fordb->elements);

        $fordb->timemodified = time();
        if ($DB->update_record_raw('local_badgecerts', $fordb)) {
            return true;
        } else {
            throw new moodle_exception('error:save', 'badges');
            return false;
        }
    }

    /**
     * Checks if badge certificate is active.
     * Used in badge certificate.
     *
     * @return bool A status indicating badge certificate is active
     */
    public function is_active() {
        if (($this->status == CERT_STATUS_ACTIVE) ||
                ($this->status == CERT_STATUS_ACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Use to get the name of badge certificate status.
     *
     */
    public function get_status_name() {
        return get_string('badgecertificatestatus_' . $this->status, 'local_badgecerts');
    }

    /**
     * Use to set badge certificate status.
     * Only active badge certificates can be used.
     *
     * @param int $status Status from CERT_STATUS constants
     */
    public function set_status($status = 0) {
        $this->status = $status;
        unset($this->certbgimage);
        $this->save();
    }

    /**
     * Checks if badge certificate is locked.
     * Used in badge certificate editing.
     *
     * @return bool A status indicating badge certificate is locked
     */
    public function is_locked() {
        if (($this->status == CERT_STATUS_ACTIVE_LOCKED) ||
                ($this->status == CERT_STATUS_INACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Fully deletes the badge certificate.
     */
    public function delete() {
        global $DB;

        $fs = get_file_storage();

        // Delete badge certificate images.
        $certcontext = $this->get_context();
        $fs->delete_area_files($certcontext->id, 'certificates', 'certbgimage', $this->id);

        // Finally, remove badge certificate itself.
        $DB->delete_records('local_badgecerts', array('id' => $this->id));
    }

    /**
     * Generates badge certificate preview in PDF format.
     */
    public function preview_badge_certificate() {
        global $CFG;
        require_once($CFG->libdir . '/tcpdf/tcpdf.php');

        $pdf = new TCPDF($this->orientation, $this->unit, $this->format, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        // Remove default header/footer.
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // Override default margins.
        $pdf->SetMargins(0, 0, 0, true);
        // Set auto page breaks.
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        // Set image scale factor.
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Set default font subsetting mode.
        $pdf->setFontSubsetting(false);

        // Add badge certificate background image.
        if ($this->certbgimage) {
            // Add a page
            // This method has several options, check the source code documentation for more information.
            $pdf->AddPage();

            // Get the current page break margin.
            $breakmargin = $pdf->getBreakMargin();
            // Get current auto-page-break mode.
            $autopagebreak = $pdf->getAutoPageBreak();
            // Disable auto-page-break.
            $pdf->SetAutoPageBreak(false, 0);

            $template = file_get_contents($this->certbgimage);
            // Replace all placeholder tags.
            $now = time();
            $placeholders = array(
                '[[recipient-fname]]', // Adds the recipient's first name.
                '[[recipient-lname]]', // Adds the recipient's last name.
                '[[recipient-flname]]', // Adds the recipient's full name (first, last).
                '[[recipient-lfname]]', // Adds the recipient's full name (last, first).
                '[[recipient-email]]', // Adds the recipient's email address.
                '[[issuer-name]]', // Adds the issuer's name or title.
                '[[issuer-contact]]', // Adds the issuer's contact information.
                '[[badge-name]]', // Adds the badge's name or title.
                '[[badge-desc]]', // Adds the badge's description.
                '[[badge-number]]', // Adds the badge's ID number.
                '[[badge-course]]', // Adds the name of the course where badge was awarded.
                '[[badge-hash]]', // Adds the badge hash value.
                '[[datetime-Y]]', // Adds the year.
                '[[datetime-d.m.Y]]', // Adds the date in dd.mm.yyyy format.
                '[[datetime-d/m/Y]]', // Adds the date in dd/mm/yyyy format.
                '[[datetime-F]]', // Adds the date (used in DB datestamps).
                '[[datetime-s]]', // Adds Unix Epoch Time timestamp.
                '[[booking-name]]', // Adds the seminar instance name.
                '[[booking-title]]', // Adds the seminar title.
                '[[booking-startdate]]', // Adds the seminar start date.
                '[[booking-enddate]]', // Adds the seminar end date.
                '[[booking-duration]]', // Adds the seminar duration.
                '[[recipient-birthdate]]', // Adds the recipient's date of birth.
                '[[recipient-institution]]', // Adds the institution where the recipient is employed.
                '[[badge-date-issued]]', // Adds the date when badge was issued.
            );
            $values = array(
                get_string('preview:recipientfname', 'local_badgecerts'),
                get_string('preview:recipientlname', 'local_badgecerts'),
                get_string('preview:recipientflname', 'local_badgecerts'),
                get_string('preview:recipientlfname', 'local_badgecerts'),
                get_string('preview:recipientemail', 'local_badgecerts'),
                get_string('preview:issuername', 'local_badgecerts'),
                get_string('preview:issuercontact', 'local_badgecerts'),
                get_string('preview:badgename', 'local_badgecerts'),
                get_string('preview:badgedesc', 'local_badgecerts'),
                $this->id,
                get_string('preview:badgecourse', 'local_badgecerts'),
                sha1(rand() . $this->usercreated . $this->id . $now),
                strftime('%Y', $now),
                userdate($now, get_string('datetimeformat', 'local_badgecerts')),
                userdate($now, get_string('datetimeformat', 'local_badgecerts')),
                strftime('%F', $now),
                strftime('%s', $now),
                get_string('preview:bookinginstancename', 'local_badgecerts'),
                get_string('preview:seminartitle', 'local_badgecerts'),
                userdate(strtotime('- 2 month', $now), get_string('datetimeformat', 'local_badgecerts')),
                userdate(strtotime('- 1 month', $now), get_string('datetimeformat', 'local_badgecerts')),
                get_string('preview:seminarduration', 'local_badgecerts'),
                userdate(strtotime('1 January 1970'), get_string('datetimeformat', 'local_badgecerts')),
                get_string('preview:recipientinstitution', 'local_badgecerts'),
                userdate($now, get_string('datetimeformat', 'local_badgecerts')),
            );
            $template = str_replace($placeholders, $values, $template);

            $pdf->ImageSVG('@' . $template, 0, 0, 0, 0, '', '', '', 0, true);

            // Restore auto-page-break status.
            $pdf->SetAutoPageBreak($autopagebreak, $breakmargin);
            // Set the starting point for the page content.
            $pdf->setPageMark();
        }

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('preview_' . $this->name . '.pdf', 'I');
    }

}

/**
 * Insert to log table when user transfer certificate.
 *
 * @param int $certid Certificate id
 * @param int $userid User id
 */
function local_badgecerts_insert_to_log($certid = null, $userid = null) {
    global $DB, $USER;

    if (!$certid && !$userid) {
        return false;
    }

    $timelog = new stdClass();
    $timelog->badgecertificateid = $certid;
    $timelog->transfereruserid = $USER->id;
    $timelog->userid = $userid;
    $timelog->created = time();

    $DB->insert_record('local_badgecerts_trasnfers', $timelog);
}

/**
 * Get all badge certificates.
 *
 * @param int    $type     Type of badges to return
 * @param int    $courseid Course ID for course badges
 * @param string $sort     An SQL field to sort by
 * @param string $dir      The sort direction ASC|DESC
 * @param int    $page     The page or records to return
 * @param int    $perpage  The number of records to return per page
 * @param int    $user     User specific search
 *
 * @return array $badge Array of records matching criteria
 */
function badges_get_certificates($type, $courseid = 0, $sort = '', $dir = '', $page = 0, $perpage = CERT_PERPAGE, $user = 0) {
    global $DB;
    $records = array();
    $params = array();

    $params['type'] = $type;
    $where = "bc.type = :type";

    $userfields = array('bc.id, bc.name, bc.status');
    $usersql = "";
    $fields = implode(', ', $userfields);

    if ($courseid != 0) {
        $where .= "AND bc.courseid = :courseid ";
        $params['courseid'] = $courseid;
    }

    $sorting = (($sort != '' && $dir != '') ? 'ORDER BY ' . $sort . ' ' . $dir : '');

    $sql = "SELECT $fields FROM {local_badgecerts} bc $usersql WHERE $where $sorting";
    $records = $DB->get_records_sql($sql, $params, intval($page) * intval($perpage), $perpage);

    $certs = array();
    foreach ($records as $r) {
        $cert = new badge_certificate($r->id);
        $certs[$r->id] = $cert;
        $certs[$r->id]->statstring = $cert->get_status_name();
    }
    return $certs;
}

/**
 * Get all badge certificates for courseid.
 * TO-DO: Odstrani, ker se ne potrebuje več!
 *
 * @param int $courseid Course ID for course badges
 *
 * @return array $badge Array of records matching criteria
 */
function badges_get_certificates_for_courseid($courseid = 0) {
    global $DB;
    $records = array();
    $params = array();
    $where = "bc.courseid = :courseid";
    $params['courseid'] = $courseid;

    $userfields = array('bc.id, bc.name, bc.status');
    $usersql = "";
    $fields = implode(', ', $userfields);

    $sql = "SELECT $fields FROM {local_badgecerts} bc $usersql WHERE $where";
    $records = $DB->get_records_sql($sql, $params);

    $certs = array();
    foreach ($records as $r) {
        $cert = new badge_certificate($r->id);
        $certs[$r->id] = $cert;
        $certs[$r->id]->statstring = $cert->get_status_name();
    }
    return $certs;
}

/**
 * Returns array of assigned badges that badge certificates
 * are already assigned to.
 *
 * @param int $courseid course ID.
 * @param int $certid   Certificate instance.
 *
 * @return array Array containing all the assigned badges
 */
function get_assigned_badge_options($courseid, $certid) {
    global $DB;

    $records = array();
    $params = array();
    $where = ' b.courseid = :courseid AND (b.status = 1 OR b.status = 3) AND b.id = :certid ';
    $params['courseid'] = $courseid;
    $params['certid'] = $certid;

    $userfields = array('b.id, b.name');
    $usersql = '';
    $fields = implode(', ', $userfields);

    $sorting = 'ORDER BY b.name ASC ';

    $sql = "SELECT $fields FROM {badge} b $usersql WHERE $where $sorting";
    $records = $DB->get_records_sql($sql, $params);

    $options = array();
    foreach ($records as $record) {
        $options[$record->id] = $record->name;
    }

    return $options;
}

/**
 * Return true if is connected to booking.
 *
 * @param int $certid ID of badge
 *
 * @return bool return true, if is connected to booking
 */
function has_booking($certid) {
    global $DB;

    $bcert = $DB->get_record('local_badgecerts', array('id' => $certid));

    if ($bcert->bookingid > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get badge certificates for a specific user.
 *
 * @param int $userid User ID
 * @param int $courseid Badge certs earned by a user in a specific course
 * @param int $page The page or records to return
 * @param int $perpage The number of records to return per page
 * @param string $search A simple string to search for
 * @param bool $onlypublic Return only public badges
 * @return array of certs ordered by decreasing date of issue
 */
function badges_get_user_certificates($userid, $courseid = 0, $page = 0, $perpage = 0, $search = '', $onlypublic = false) {
    global $DB;
    $certs = array();

    $quizgrading = '';
    $booking = '';

    if (utils::check_mod_quizgrading()) {
        $quizgrading = 'OR (SELECT CASE WHEN bc.quizgradingid > 0 AND bc.certtype = 3 THEN
            (SELECT
                CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
            FROM
                {quizgrading_results} AS qr
            WHERE
                qr.userid = u.id)
            ELSE 0 END) = 1';
    }

    if (utils::check_mod_booking()) {
        $booking = 'OR (SELECT
        CASE WHEN bc.bookingid > 0 AND bc.certtype = 1 THEN
                (SELECT
                        CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                    FROM
                        {booking_answers} ans
                    LEFT JOIN
                        {booking_options} bo ON ans.optionid = bo.id
                    WHERE
                        ans.bookingid = (SELECT
                                instance
                            FROM
                                {course_modules} AS cm
                            WHERE
                                cm.id = bc.bookingid)
                            AND ans.userid = u.id AND ans.completed = 1 AND CASE WHEN bc.startdate != 0
                            THEN bo.coursestarttime >= bc.startdate AND bo.courseendtime <= bc.enddate ELSE 1 = 1 END) ELSE
                0 END = 1
        OR (SELECT
        CASE WHEN bc.bookingid > 0 AND bc.certtype = 4 THEN
                (SELECT
                        CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                    FROM
                        {booking_answers} AS ans
                    LEFT JOIN
                        {booking_options} bo ON ans.optionid = bo.id
                    WHERE
                        ans.bookingid = (SELECT
                                instance
                            FROM
                                {course_modules} AS cm
                            WHERE
                                cm.id = bc.bookingid)
                            AND ans.userid = u.id AND ans.completed = 1 AND CASE WHEN bc.startdate != 0
                             THEN bo.coursestarttime >= bc.startdate AND bo.courseendtime <= bc.enddate ELSE 1 = 1 END) ELSE
                0 END) = 1
        OR (SELECT
        CASE WHEN bc.bookingid > 0 AND bc.certtype = 2 THEN
                (SELECT
                        CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
                    FROM
                        {booking_teachers} tch
                    LEFT JOIN
                        {booking_options} bo ON tch.optionid = bo.id
                    WHERE
                        tch.bookingid = (SELECT
                                instance
                            FROM
                                {course_modules} AS cm
                            WHERE
                                cm.id = bc.bookingid)
                            AND tch.userid = u.id AND tch.completed = 1 AND
                            CASE WHEN bc.startdate != 0 THEN bo.coursestarttime >= bc.startdate
                            AND bo.courseendtime <= bc.enddate ELSE 1 = 1 END) ELSE
                0 END
     = 1))';
    }

    $sql = "SELECT
                bc.id,
                bc.name,
                b.courseid,
                b.id badgeid,
                bi.uniquehash,
                b.type,
                bc.bookingid,
                bc.certtype
            FROM {local_badgecerts} bc
            LEFT JOIN {badge_issued} bi
                ON bi.badgeid = bc.certid
            LEFT JOIN {user} u
                ON u.id = bi.userid
            LEFT JOIN {badge} b
                ON b.id = bi.badgeid
            WHERE bi.userid = ?
                AND bc.certid IS NOT null
                AND bc.status >= 1 AND (
            (SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END) = 1
            {$quizgrading}
            {$booking}
            )";
    $params[] = $userid;

    if (!empty($search)) {
        $sql .= ' AND (' . $DB->sql_like('b.name', '?', false) . ') ';
        $params[] = "%$search%";
    }
    if ($onlypublic) {
        $sql .= ' AND (bi.visible = 1) ';
    }

    if ($courseid != 0) {
        $sql .= ' AND (b.courseid = ?) ';
        $params[] = $courseid;
    }

    $sql .= ' ORDER BY bi.dateissued DESC';

    $certs = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

    return $certs;
}

/**
 * Check if user has the privileges to bulk generate badge certificates.
 *
 * @param int $currentcourseid CMID.
 *
 * @return void
 */
function user_can_bulk_generate_certificates_in_course($currentcourseid) {
    global $USER, $DB;

    $fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => 'bulkGenCerts'));
    $courseid = $DB->get_field('user_info_data', 'data', array('userid' => $USER->id, 'fieldid' => $fieldid));
    if ($courseid == $currentcourseid) {
        // User has the right to bulk generate badge certificates.
        return true;
    } else {
        return false;
    }
}

/**
 * Get bookingoptionid - from booking module.
 *
 * @param int $bookingid Booking instance.
 * @param int $userid    User ID.
 *
 * @return void
 */
function booking_getbookingoptionid($bookingid = null, $userid = null) {
    global $DB;

    if (is_null($userid) || is_null($bookingid)) {
        return false;
    }

    $ba = $DB->get_record('booking_answers', array('completed' => '1', 'userid' => $userid, 'bookingid' => $bookingid));

    if ($ba === false) {
        return (int) 0;
    } else {
        return (int) $ba->optionid;
    }
}

/**
 * Get bookingoptionid - from booking module.
 *
 * @param int      $bookingid Booking instance.
 * @param int      $userid    User ID.
 * @param stdClass $cert      Cert obejct.
 *
 * @return void
 */
function booking_getbookingoptionsid($bookingid = null, $userid = null, $cert = null) {
    global $DB;

    if (is_null($userid) || is_null($bookingid)) {
        return false;
    }

    if ($cert->enablebookingoptions) {
        $yesno = ($cert->optionsincexc == 1 ? '' : 'NOT');
        $exsql = " AND ba.optionid {$yesno} IN ({$cert->bookingoptions})";
    } else {
        $exsql = '';
    }

    $sql = "SELECT ba.optionid FROM ";

    switch ($cert->certtype) {
        case 1:
            $sql .= "{booking_answers} ba ";
            break;

        case 2:
            $sql .= "{booking_teachers} ba ";
            break;

        default:
            return (int) 0;
            break;
    }

    $r = array();

    if ($cert->startdate != 0) {
        $sql .= "LEFT JOIN {booking_options} bo ON bo.id = ba.optionid ";
    }

    $sql .= "WHERE ba.completed = 1 AND ba.userid = ? AND ba.bookingid = ? {$exsql} ";

    $conditions = array();
    $conditions[] = $userid;
    $conditions[] = $bookingid;

    if ($cert->startdate != 0) {
        $sql .= "AND bo.coursestarttime >= ? AND bo.courseendtime <= ? ";
        $conditions[] = $cert->startdate;
        $conditions[] = $cert->enddate;
    }

    $ba = $DB->get_records_sql($sql, $conditions);

    foreach ($ba as $value) {
        $r[] = (int) $value->optionid;
    }

    return array_unique($r);
}

/**
 * Get bookingoptions - from booking module
 *
 * @param int $cmid     CMID.
 * @param int $optionid Option ID.
 *
 * @return void
 */
function booking_getbookingoptions($cmid = null, $optionid = null) {
    if (is_null($optionid)) {
        return false;
    }

    $booking = new \mod_booking\booking_option($cmid, $optionid);
    $booking->apply_tags();

    if (empty($booking)) {
        return false;
    } else {
        return array(
            'name' => $booking->booking->settings->name,
            'text' => $booking->option->text,
            'coursestarttime' => $booking->option->coursestarttime,
            'courseendtime' => $booking->option->courseendtime,
            'duration' => $booking->booking->settings->duration);
    }
}

/**
 * Get user data for badge.
 *
 * @param int $userid User ID.
 *
 * @return void
 */
function getuserdata($userid = null) {
    global $DB;
    // Get a recipient from database.
    $namefields = get_all_user_name_fields(true, 'u');
    $user = $DB->get_record_sql("SELECT u.id, $namefields, u.deleted,
                                            u.email AS accountemail, b.email AS backpackemail
                    FROM {user} u LEFT JOIN {badge_backpack} b ON u.id = b.userid
                    WHERE u.id = :userid", array('userid' => $userid));
    // Add custom profile field 'Datumrojstva' value.
    $fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => 'Datumrojstva'));
    if ($fieldid && $birthdate = $DB->get_field('user_info_data', 'data',
        array('userid' => $userid, 'fieldid' => $fieldid))) {
        $user->birthdate = $birthdate;
    } else {
        $user->birthdate = null;
    }
    // Add custom profile field 'VIZ' value.
    $fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => 'VIZ'));
    if ($fieldid && $institution = $DB->get_field('user_info_data', 'data',
        array('userid' => $userid, 'fieldid' => $fieldid))) {
        $user->institution = $institution;
    } else {
        $user->institution = null;
    }

    return $user;
}

/**
 * Get certain data gor badge.
 *
 * @param stdClass $cert  Certificate object.
 * @param stdClass $badge Badge obejct.
 *
 * @return void
 */
function get_badge_data($cert, $badge) {
    $assertion = new core_badges_assertion($badge->hash);
    $cert->issued = $assertion->get_badge_assertion();
    $cert->badgeclass = $assertion->get_badge_class();

    $cert->recipient = getuserdata($badge->userid);
    $user = $cert->recipient;

    $booking = new StdClass();
    $booking->name = get_string('titlenotset', 'local_badgecerts');
    $booking->title = get_string('titlenotset', 'local_badgecerts');
    $booking->startdate = get_string('datenotdefined', 'local_badgecerts');
    $booking->enddate = get_string('datenotdefined', 'local_badgecerts');
    $booking->duration = 0;

    return array($cert, $badge, $booking, $user);
}

/**
 * Get all certificates for courseid - for API!
 *
 * @param int $courseid CMID.
 *
 * @return void
 */
function get_all_certificates($courseid = null) {
    global $DB;
    if (is_null($courseid)) {
        return false;
    }

    // TO-DO: Popravi, ker sem spremenil...
    $allcertificates = badges_get_certificates_for_courseid($courseid);

    $bulkcerts = array();

    foreach ($allcertificates as $certificate) {
        $badgeid = $DB->get_field('badge', '*', array('certid' => $certificate->id));
        $sql = "SELECT bi.userid, bi.uniquehash AS hash
                FROM {badge_issued} bi
                WHERE bi.badgeid = :badgeid";
        $badges = $DB->get_records_sql($sql, array('badgeid' => $badgeid));

        $cert = new badge_certificate($certificate->id);

        foreach ($badges as $badge) {
            list($cert, $badge, $booking, $user) = get_badge_data($cert, $badge);

            if ($cert->bookingid > 0 && in_array($cert->certtype, array(1, 2))) {
                $optionid = booking_getbookingoptionid($cert->bookingid, $badge->userid);
                if (isset($optionid) && $optionid > 0) {
                    $coursemodule = get_coursemodule_from_id('booking', $cert->bookingid);
                    $options = booking_getbookingoptions($coursemodule->id, $optionid);
                    // Set seminar title.
                    if (isset($options['text']) && !empty($options['text'])) {
                        $booking->title = $options['text'];
                    }
                    // Set seminar start date.
                    if (isset($options['coursestarttime']) && !empty($options['coursestarttime'])) {
                        $booking->startdate = userdate((int) $options['coursestarttime'], get_string('strftimedatefullshort'));
                    }
                    // Set seminar end date.
                    if (isset($options['courseendtime']) && !empty($options['courseendtime'])) {
                        $booking->enddate = userdate((int) $options['courseendtime'], get_string('strftimedatefullshort'));
                    }
                    // Set seminar duration.
                    if (isset($options['duration']) && !empty($options['duration'])) {
                        $booking->title = $options['duration'];
                    }
                    // Set seminar duration.
                    if (isset($options['name']) && !empty($options['name'])) {
                        $booking->name = $options['name'];
                    }
                }
            }
            // Replace all placeholder tags.
            $now = time();
            // Set account email if backpack email is not set up and/or connected.
            if (isset($cert->recipient->backpackemail) && !empty($cert->recipient->backpackemail)) {
                $recipientemail = $cert->recipient->backpackemail;
            } else {
                $recipientemail = $cert->recipient->accountemail;
            }

            $owncert = array();

            $owncert['recipientFirstName'] = $cert->recipient->firstname;
            $owncert['recipientLastName'] = $cert->recipient->lastname;
            $owncert['recipientEmail'] = $recipientemail;
            $owncert['issuerName'] = $cert->issuername;
            $owncert['issuerContact'] = $cert->issuercontact;
            $owncert['badgeName'] = $cert->badgeclass['name'];
            $owncert['badgeDesc'] = $cert->badgeclass['description'];
            $owncert['badgeNumber'] = $cert->id;
            $owncert['badgeCourse'] = $DB->get_field('course', 'fullname', array('id' => $cert->courseid));
            $owncert['badgeHash'] = sha1(rand() . $cert->usercreated . $cert->id . $now);
            $owncert['bookingTitle'] = $booking->title;
            $owncert['bookingStartdate'] = $booking->startdate;
            $owncert['bookingEnddate'] = $booking->enddate;
            $owncert['bookingDuration'] = $booking->duration;
            $owncert['bookingName'] = $booking->name;
            $owncert['recipientBirthdate'] = userdate((int) $cert->recipient->birthdate, get_string('strftimedatefullshort'));
            $owncert['recipientInstitution'] = $cert->recipient->institution;
            $owncert['badgeDateIssued'] = userdate((int) $cert->issued, get_string('strftimedatefullshort'));

            $bulkcerts[] = $owncert;
        }
    }

    return $bulkcerts;
}

/**
 * Fix encoding.
 *
 * @param string $instr Input string.
 *
 * @return string Fixed string.
 */
function fixencoding($instr) {
    $curencoding = mb_detect_encoding($instr);
    if ($curencoding == "UTF-8" && mb_check_encoding($instr, "UTF-8")) {
        return $instr;
    } else {
        return utf8_encode($instr);
    }
}

/**
 * Generate placeholders.
 *
 * @param object $cert          Certificate object.
 * @param object $booking       Booking object.
 * @param object $quizreporting Quizreporting object.
 *
 * @return array Returns array of values to change in template.
 */
function get_placeholders($cert, $booking, $quizreporting = null) {
    global $DB;

    if (is_null($quizreporting)) {
        $quizreporting = new stdClass();
        $quizreporting->quizname = "";
        $quizreporting->sumgrades = "";
        $quizreporting->firstname = "";
        $quizreporting->lastname = "";
        $quizreporting->email = "";
        $quizreporting->institution = "";
        $quizreporting->dosezeno_tock = "";
        $quizreporting->kazenske_tocke = "";
        $quizreporting->moznih_tock = "";
        $quizreporting->procent = "";
        $quizreporting->vprasanja = "";
        $quizreporting->status_kviza = "";
        $quizreporting->datum_resitve = "";
        $quizreporting->datum_vpisa = "";
        $quizreporting->uvrstitev_posamezniki = "";
        $quizreporting->uvrstitev_skupina = "";
        $quizreporting->organizator = "";
        $quizreporting->lokacija = "";
    }

    // Replace all placeholder tags.
    $now = time();
    // Set account email if backpack email is not set up and/or connected.
    if (isset($cert->recipient->backpackemail) && !empty($cert->recipient->backpackemail)) {
        $recipientemail = $cert->recipient->backpackemail;
    } else {
        $recipientemail = $cert->recipient->accountemail;
    }
    $placeholders = array(
        '[[recipient-fname]]', // Adds the recipient's first name.
        '[[recipient-lname]]', // Adds the recipient's last name.
        '[[recipient-flname]]', // Adds the recipient's full name (first, last).
        '[[recipient-lfname]]', // Adds the recipient's full name (last, first).
        '[[recipient-email]]', // Adds the recipient's email address.
        '[[issuer-name]]', // Adds the issuer's name or title.
        '[[issuer-contact]]', // Adds the issuer's contact information.
        '[[badge-name]]', // Adds the badge's name or title.
        '[[badge-desc]]', // Adds the badge's description.
        '[[badge-number]]', // Adds the badge's ID number.
        '[[badge-course]]', // Adds the name of the course where badge was awarded.
        '[[badge-hash]]', // Adds the badge hash value.
        '[[datetime-Y]]', // Adds the year.
        '[[datetime-d.m.Y]]', // Adds the date in dd.mm.yyyy format.
        '[[datetime-d/m/Y]]', // Adds the date in dd/mm/yyyy format.
        '[[datetime-F]]', // Adds the date (used in DB datestamps).
        '[[datetime-s]]', // Adds Unix Epoch Time timestamp.
        '[[booking-name]]', // Adds the seminar instance name.
        '[[booking-title]]', // Adds the seminar title.
        '[[booking-startdate]]', // Adds the seminar start date.
        '[[booking-enddate]]', // Adds the seminar end date.
        '[[booking-duration]]', // Adds the seminar duration.
        '[[recipient-birthdate]]', // Adds the recipient's date of birth.
        '[[recipient-institution]]', // Adds the institution where the recipient is employed.
        '[[badge-date-issued]]', // Adds the date when badge was issued.
        // Quiz Grading.
        '[[qg-quizname]]',
        '[[qg-sumgrades]]',
        '[[qg-firstname]]',
        '[[qg-up-firstname]]',
        '[[qg-lastname]]',
        '[[qg-up-lastname]]',
        '[[qg-email]]',
        '[[qg-institution]]',
        '[[qg-up-institution]]',
        '[[qg-dosezeno_tock]]',
        '[[qg-kazenske_tocke]]',
        '[[qg-moznih_tock]]',
        '[[qg-procent]]',
        '[[qg-vprasanja]]',
        '[[qg-status_kviza]]',
        '[[qg-datum_resitve]]',
        '[[qg-datum_vpisa]]',
        '[[qg-datum_rojstva]]',
        '[[qg-uvrstitev_posamezniki]]',
        '[[qg-uvrstitev_skupina]]',
        '[[qg-organizator]]',
        '[[qg-lokacija]]',
        '[[qg-up-organizator]]',
        '[[qg-up-lokacija]]'
    );
    $values = array(
        $cert->recipient->firstname,
        $cert->recipient->lastname,
        $cert->recipient->firstname . ' ' . $cert->recipient->lastname,
        $cert->recipient->lastname . ' ' . $cert->recipient->firstname,
        $recipientemail,
        $cert->issuername,
        $cert->issuercontact,
        $cert->name,
        $cert->description,
        $cert->id,
        $DB->get_field('course', 'fullname', array('id' => $cert->courseid)),
        sha1(rand() . $cert->usercreated . $cert->id . $now),
        strftime('%Y', $now),
        userdate($now, get_string('datetimeformat', 'local_badgecerts')),
        userdate($now, get_string('datetimeformat', 'local_badgecerts')),
        strftime('%F', $now),
        strftime('%s', $now),
        $booking->name,
        $booking->title,
        $booking->startdate,
        $booking->enddate,
        $booking->duration,
        userdate((int) $cert->recipient->birthdate, get_string('datetimeformat', 'local_badgecerts')),
        $cert->recipient->institution,
        userdate((int) $cert->usercreated, get_string('datetimeformat', 'local_badgecerts')),
        // Quiz Grading.
        $quizreporting->quizname,
        $quizreporting->sumgrades,
        $quizreporting->firstname,
        mb_strtoupper($quizreporting->firstname, 'UTF-8'),
        $quizreporting->lastname,
        mb_strtoupper($quizreporting->lastname, 'UTF-8'),
        $quizreporting->email,
        $quizreporting->institution,
        mb_strtoupper($quizreporting->institution, 'UTF-8'),
        $quizreporting->dosezeno_tock,
        $quizreporting->kazenske_tocke,
        $quizreporting->moznih_tock,
        $quizreporting->procent,
        $quizreporting->vprasanja,
        ($quizreporting->status_kviza == 1 ? get_string('jeopravil', 'local_badgecerts') : get_string('niopravil',
            'local_badgecerts')),
        isset($quizreporting->datum_resitve) ? userdate($quizreporting->datum_resitve,
            get_string('datetimeformat', 'local_badgecerts')) : '',
        isset($quizreporting->datum_vpisa) ? userdate($quizreporting->datum_vpisa,
            get_string('datetimeformat', 'local_badgecerts')) : '',
        isset($quizreporting->datum_rojstva) ? userdate($quizreporting->datum_rojstva,
            get_string('datetimeformat', 'local_badgecerts')) : '',
        $quizreporting->uvrstitev_posamezniki,
        $quizreporting->uvrstitev_skupina,
        $quizreporting->organizator,
        $quizreporting->lokacija,
        mb_strtoupper($quizreporting->organizator, 'UTF-8'),
        mb_strtoupper($quizreporting->lokacija, 'UTF-8')
    );

    $i = 1;

    $r = explode("||", $booking->title);

    $pl = array();
    $val = array();

    foreach ($r as $value) {
        $pl[] = "[[booking-title-{$i}]]";
        $val[] = $value;

        $i++;
    }

    while ($i <= 10) {
        $pl[] = "[[booking-title-{$i}]]";
        $val[] = "";

        $i++;
    }

    $placeholders = array_merge($placeholders, $pl);
    $values = array_merge($values, $val);

    return array('placeholders' => $placeholders, 'values' => $values);
}

/**
 * Bulk generate badge certificates - only for submited users.
 *
 * @param int      $certid Certificate ID.
 * @param stdClass $badges Badge.
 * @param string   $dest   Download or show.
 *
 * @return void
 */
function bulk_generate_certificates($certid, $badges, $dest = 'D') {
    global $CFG, $DB;

    // Generate badge certificate for each of the issued badges.
    require_once($CFG->libdir . '/tcpdf/tcpdf.php');

    $cert = new badge_certificate($certid);
    $pdf = new TCPDF($cert->orientation, $cert->unit, $cert->format, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    // Remove default header/footer.
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    // Set default monospaced font.
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Override default margins.
    $pdf->SetMargins(0, 0, 0, true);
    // Set auto page breaks.
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    // Set image scale factor.
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // Set default font subsetting mode.
    $pdf->setFontSubsetting(false);

    if ($cert->bookingid > 0) {
        $coursemodule = get_coursemodule_from_id('booking', $cert->bookingid);
        $bookingid = $coursemodule->instance;
    }

    // Add badge certificate background image.
    if ($cert->certbgimage && !empty($badges)) {
        foreach ($badges as $badge) {
            list($cert, $badge, $booking, $user) = get_badge_data($cert, $badge);

            if ($cert->bookingid > 0 && in_array($cert->certtype, array(1, 2))) {
                $optionids = booking_getbookingoptionsid($bookingid, $badge->userid, $cert);
                foreach ($optionids as $optionid) {
                    if (isset($optionid) && $optionid > 0) {
                        $options = booking_getbookingoptions($coursemodule->id, $optionid);

                        // Set seminar title.
                        if (isset($options['name']) && !empty($options['name'])) {
                            $booking->name = $options['name'];
                        } else {
                            $booking->name = get_string('titlenotset', 'local_badgecerts');
                        }

                        // Set seminar title.
                        if (isset($options['text']) && !empty($options['text'])) {
                            $booking->title = $options['text'];
                        } else {
                            $booking->title = get_string('titlenotset', 'local_badgecerts');
                        }

                        // Set seminar start date.
                        if (isset($options['coursestarttime']) && !empty($options['coursestarttime'])) {
                            $booking->startdate = userdate((int) $options['coursestarttime'], get_string('strftimedate'));
                        } else {
                            $booking->startdate = get_string('datenotdefined', 'local_badgecerts');
                        }

                        // Set seminar end date.
                        if (isset($options['courseendtime']) && !empty($options['courseendtime'])) {
                            $booking->enddate = userdate((int) $options['courseendtime'], get_string('strftimedate'));
                        } else {
                            $booking->enddate = get_string('datenotdefined', 'local_badgecerts');
                        }

                        // Set seminar duration.
                        if (isset($options['duration']) && !empty($options['duration'])) {
                            $booking->duration = $options['duration'];
                        } else {
                            $booking->duration = 0;
                        }

                        add_pdf_page($cert, $badge, $pdf, $booking, null, $user);
                    }
                }
            } else if ($cert->bookingid > 0 && $cert->certtype == 4) {
                $conditions = array();
                $conditions[] = $badge->userid;
                $conditions[] = $bookingid;

                $timelimit = '';

                if ($cert->startdate != 0) {
                    $timelimit = "AND bo.coursestarttime >= ? AND bo.courseendtime <= ?";
                    $conditions[] = $cert->startdate;
                    $conditions[] = $cert->enddate;
                }

                $result = $DB->get_record_sql("SELECT SUM(ROUND(bo.duration / 60 / 60, 0))
                    duration, GROUP_CONCAT(bo.text SEPARATOR '||') text FROM {booking_answers} ba LEFT JOIN {booking_options} bo ON
                    ba.optionid = bo.id WHERE ba.userid = ? AND ba.completed = 1 AND bo.bookingid = ? {$timelimit}", $conditions);

                $booking->duration = $result->duration;
                $booking->title = $result->text;

                add_pdf_page($cert, $badge, $pdf, $booking, null, $user);
            } else if ($cert->quizgradingid > 0 && $cert->certtype == 3) {

                $quizreporting = $DB->get_records_sql("SELECT *
                            FROM {quizgrading_results}
                            WHERE quizgradingid = :quizgradnigid AND userid = :userid",
                            array('quizgradnigid' => $cert->quizgradingid,
                                'userid' => $cert->recipient->id));

                foreach ($quizreporting as $quizreport) {
                    add_pdf_page($cert, $badge, $pdf, $booking, $quizreport, $user);
                }
            } else {
                add_pdf_page($cert, $badge, $pdf, $booking, null, $user);
            }
        }

        // Close and output PDF document.
        // This method has several options, check the source code documentation for more information.
        switch ($dest) {
            case 'S':
                return $pdf->Output($cert->name . '.pdf', $dest);
                break;

            default:
                $pdf->Output($cert->name . '.pdf', $dest);
                break;
        }
    }
}

/**
 * Generate certificate in pdf.
 *
 * @param integer $cert          Certificate ID.
 * @param integer $badge         Badge ID.
 * @param object  $pdf           PDF object.
 * @param object  $booking       Booking object.
 * @param object  $quizreporting Quiz reporting object.
 * @param object  $user          User object.
 */
function add_pdf_page($cert, $badge, &$pdf, $booking, $quizreporting = null, $user) {
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    // Get the current page break margin.
    $breakmargin = $pdf->getBreakMargin();
    // Get current auto-page-break mode.
    $autopagebreak = $pdf->getAutoPageBreak();
    // Disable auto-page-break.
    $pdf->SetAutoPageBreak(false, 0);

    $template = file_get_contents($cert->certbgimage);

    $placeholders = get_placeholders($cert, $booking, $quizreporting);

    $template = str_replace($placeholders['placeholders'], $placeholders['values'], $template);
    $pdf->ImageSVG('@' . $template, 0, 0, 0, 0, '', '', '', 0, true);
    // Restore auto-page-break status.
    $pdf->SetAutoPageBreak($autopagebreak, $breakmargin);
    // Set the starting point for the page content.
    $pdf->setPageMark();

    if ($cert->qrshow) {

        $tmpqrdata = '';

        switch ($cert->qrdata) {
            case 0:
                $tmpqrdata = $user->id;
                break;

            case 1:
                $tmpqrdata = $user->username;
                break;

            default:
                break;
        }

        $pdf->write2DBarcode($tmpqrdata, 'QRCODE,H', $cert->qrx, $cert->qry, $cert->qrw, $cert->qrh);
    }

    local_badgecerts_insert_to_log($cert->id, $badge->userid);
}

/**
 * Navigation.
 *
 * @param navigation_node $parentnode Navigation node.
 * @param stdClass        $user       User class.
 * @param context_user    $context    Context.
 * @param stdClass        $course     Course object.
 *
 * @return void
 */
function local_badgecerts_extend_navigation_user(navigation_node $parentnode, stdClass $user,
    context_user $context, stdClass $course) {
    global $PAGE;

    if (isloggedin()) {
        if (has_any_capability(array(
                    'local/badgecerts:viewcertificates',
                    'local/badgecerts:createcertificate',
                    'local/badgecerts:configurecertificate',
                    'local/badgecerts:configureelements',
                    'local/badgecerts:deletecertificate'
                        ), $context)) {

            $url = new moodle_url('/local/badgecerts/index.php', array('type' => CERT_TYPE_COURSE, 'id' => $course->id));
            $coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);
            $coursenode->add(get_string('managebadgecertificates', 'local_badgecerts'), $url,
                navigation_node::TYPE_SETTING, null, 'userscerts', new pix_icon('i/folder', 'badgecerts'));
        }
    }
}
/**
 * See link to download own badges only on your own profile.
 *
 * @param core_user\output\myprofile\tree $tree          Navigation tree.
 * @param stdClass                        $user          User class.
 * @param boolean                         $iscurrentuser Is current user.
 * @param stdClass                        $course        Course object.
 *
 * @return void
 */
function local_badgecerts_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if ($iscurrentuser) {
        $url = new moodle_url('/local/badgecerts/mycerts.php');
        $string = get_string('mybadgecertificates', 'local_badgecerts');
        $node = new core_user\output\myprofile\node('miscellaneous', 'badgecerts', $string, null, $url);

        $tree->add_node($node);
    }
}

/**
 * Hook function to add items to the administration block.
 *
 * @param settings_navigation $nav     Which menu.
 * @param context             $context Which context.
 */
function local_badgecerts_extend_settings_navigation(settings_navigation $nav, context $context) {
    global $COURSE;

    if (isloggedin()) {
        $coursenode = $nav->get('courseadmin');
        if (has_any_capability(array(
                    'local/badgecerts:viewcertificates',
                    'local/badgecerts:createcertificate',
                    'local/badgecerts:configurecertificate',
                    'local/badgecerts:configureelements',
                    'local/badgecerts:deletecertificate'
                        ), $context)) {

            if ($coursenode) {
                $url = new moodle_url('/local/badgecerts/index.php', array('type' => CERT_TYPE_COURSE, 'id' => $COURSE->id));
                $coursenode->add(get_string('managebadgecertificates', 'local_badgecerts'), $url,
                    navigation_node::TYPE_SETTING, null, 'managecerts', new pix_icon('i/report', 'badgecerts'));
            }
        }
    }
}
