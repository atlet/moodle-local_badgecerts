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
 * Renderer for use with the badge certificates output
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

/**
 * Standard HTML output renderer for badge certificates
 */
class local_badgecerts_renderer extends plugin_renderer_base {

    // Outputs badge certificates list.
    public function print_badgecerts_list($badges, $userid, $profile = false) {
        global $USER, $CFG;
        foreach ($badges as $badge) {
            $context = ($badge->type == CERT_TYPE_SITE) ? context_system::instance() : context_course::instance($badge->courseid);
            $bname = $badge->name;

            $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1',
                            false);

            $name = html_writer::tag('span', $bname, array('class' => 'badge-name'));

            $image = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => 'badge-image'));
            if (!empty($badge->dateexpire) && $badge->dateexpire < time()) {
                $image .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'badges', userdate($badge->dateexpire)), 'moodle',
                        array('class' => 'expireimage'));
                $name .= '(' . get_string('expired', 'badges') . ')';
            }

            $download = $status = $push = '';
            if (($userid == $USER->id) && !$profile) {
                $url = new moodle_url('mycerts.php',
                        array('download' => $badge->id, 'hash' => $badge->uniquehash, 'sesskey' => sesskey()));
                $download = $this->output->action_icon($url, new pix_icon('t/download', get_string('download')));
            }

            if (!$profile) {
                $url = new moodle_url('/badges/badge.php', array('hash' => $badge->uniquehash));
            } else {
                if (!$external) {
                    $url = new moodle_url('/badges/badge.php', array('hash' => $badge->uniquehash));
                } else {
                    $hash = hash('md5', $badge->hostedUrl);
                    $url = new moodle_url('/badges/external.php', array('hash' => $hash, 'user' => $userid));
                }
            }
            $actions = html_writer::tag('div', $push . $download . $status, array('class' => 'badge-actions'));
            $items[] = html_writer::link($url, $image . $actions . $name, array('title' => $bname));
        }

        return html_writer::alist($items, array('class' => 'badges'));
    }

    // Prints a badge certificate overview infomation.
    public function print_badgecert_overview($cert, $context) {
        $display = "";

        // Badge certificate details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('badgecertificatedetails', 'local_badgecerts'),
                        array('class' => 'bold'));

        $detailstable = new html_table();
        $detailstable->attributes = array('class' => 'clearfix', 'id' => 'badgedetails');
        $detailstable->data[] = array(get_string('name') . ":", $cert->name);
        $detailstable->data[] = array(get_string('description', 'local_badgecerts') . ":", $cert->description);
        $detailstable->data[] = array(get_string('createdon', 'search') . ":", userdate($cert->timecreated));
        $display .= html_writer::table($detailstable);
        $display .= html_writer::end_tag('fieldset');

        // Issuer details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag('legend', get_string('issuerdetails', 'local_badgecerts'), array('class' => 'bold'));

        $issuertable = new html_table();
        $issuertable->attributes = array('class' => 'clearfix', 'id' => 'badgeissuer');
        $issuertable->data[] = array(get_string('issuername', 'local_badgecerts') . ":", $cert->issuername);
        $issuertable->data[] = array(get_string('contact', 'local_badgecerts') . ":",
            html_writer::tag('a', $cert->issuercontact, array('href' => 'mailto:' . $cert->issuercontact)));
        $display .= html_writer::table($issuertable);
        $display .= html_writer::end_tag('fieldset');

        return $display;
    }

    // Prints action icons for the badge certificate.
    public function print_cert_table_actions($cert, $context) {
        $actions = "";

        if (has_capability('moodle/badges:configurecertificate', $context)) {
            // Activate/deactivate badge certificate.
            if ($cert->status == CERT_STATUS_INACTIVE || $cert->status == CERT_STATUS_INACTIVE_LOCKED) {
                // "Activate" will go to another page and ask for confirmation.
                $url = new moodle_url('/local/badgecerts/action.php');
                $url->param('id', $cert->id);
                $url->param('activate', true);
                $url->param('sesskey', sesskey());
                $return = new moodle_url(qualified_me());
                $url->param('return', $return->out_as_local_url(false));
                $actions .= $this->output->action_icon($url,
                                new pix_icon('t/show', get_string('activate', 'local_badgecerts'))) . " ";
            } else {
                $url = new moodle_url(qualified_me());
                $url->param('lock', $cert->id);
                $url->param('sesskey', sesskey());
                $actions .= $this->output->action_icon($url,
                                new pix_icon('t/hide', get_string('deactivate', 'local_badgecerts'))) . " ";
            }
        }

        // Preview badge certificate.
        if (has_capability('moodle/badges:configurecertificate', $context)) {
            $url = new moodle_url('/local/badgecerts/action.php',
                    array('preview' => '1', 'id' => $cert->id, 'sesskey' => sesskey()));
            $actions .= $this->output->action_icon($url, new pix_icon('t/preview', get_string('preview'))) . " ";
        }

        // Download (bulk print/generate) badge certificates.
        if (has_capability('moodle/badges:configurecertificate', $context) &&
                user_can_bulk_generate_certificates_in_course($cert->courseid)) {
            $url = new moodle_url('/local/badgecerts/action.php',
                    array('download' => '1', 'id' => $cert->id, 'sesskey' => sesskey()));
            $actions .= $this->output->action_icon($url, new pix_icon('t/download', get_string('download'))) . " ";
        }

        // Edit badge certificate.
        if (has_capability('moodle/badges:configurecertificate', $context)) {
            $url = new moodle_url('/local/badgecerts/edit.php', array('id' => $cert->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Delete badge certificate.
        if (has_capability('moodle/badges:deletecertificate', $context)) {
            $url = new moodle_url(qualified_me());
            $url->param('delete', $cert->id);
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
        }

        return $actions;
    }

    // Outputs table of badge certificates with actions available.
    protected function render_cert_management(cert_management $certs) {
        $paging = new paging_bar($certs->totalcount, $certs->page, $certs->perpage, $this->page->url, 'page');

        // New badge certificate button.
        $htmlnew = '';
        if (has_capability('moodle/badges:createcertificate', $this->page->context)) {
            $n['type'] = $this->page->url->get_param('type');
            $n['id'] = $this->page->url->get_param('id');
            $htmlnew = $this->output->single_button(new moodle_url('/local/badgecerts/new.php', $n),
                    get_string('newbadgecertificate', 'local_badgecerts'));
        }

        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

        $sortbyname = $this->helper_sortable_heading(get_string('name'), 'name', $certs->sort, $certs->dir);
        $sortbyissuer = $this->helper_sortable_heading(get_string('issuername', 'local_badgecerts'), 'issuer',
                $certs->sort, $certs->dir);
        $sortbystatus = $this->helper_sortable_heading(get_string('status', 'badges'), 'status', $certs->sort,
                $certs->dir);
        $table->head = array(
            $sortbyname,
            $sortbyissuer,
            $sortbystatus,
            get_string('actions')
        );
        $table->colclasses = array('name', 'status', 'criteria', 'actions');

        foreach ($certs->certs as $c) {
            $style = !$c->is_active() ? array('class' => 'dimmed') : array();
            $name = html_writer::link(new moodle_url('/local/badgecerts/overview.php', array('id' => $c->id)), $c->name,
                            $style);
            $issuer = $c->issuername;
            $status = $c->statstring;

            $actions = self::print_cert_table_actions($c, $this->page->context);

            $row = array($name, $issuer, $status, $actions);
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlnew . $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    // Prints tabs for badge certificate editing.
    public function print_badgecert_tabs($certid, $context, $current = 'overview') {
        global $DB;

        // Output button => back to index page
        $cert = new badge_certificate($certid);
        $n = array('type' => $cert->type, 'id' => $cert->courseid);
        $caption = get_string('managebadgecertificates', 'local_badgecerts');
        echo $this->single_button(new moodle_url('/local/badgecerts/index.php', $n), $caption, 'get');

        // Output tabs
        $row = array();

        $row[] = new tabobject('overview', new moodle_url('/local/badgecerts/overview.php', array('id' => $certid)),
                get_string('boverview', 'local_badgecerts')
        );

        if (has_capability('moodle/badges:configurecertificate', $context)) {
            $row[] = new tabobject('details', new moodle_url('/local/badgecerts/edit.php', array('id' => $certid)),
                    get_string('bdetails', 'local_badgecerts')
            );
        }

        if (has_capability('moodle/badges:configurecertificate', $context)) {
            $row[] = new tabobject('assign', new moodle_url('/local/badgecerts/assign.php', array('id' => $certid)),
                    get_string('bassign', 'local_badgecerts')
            );
        }

        echo $this->tabtree($row, $current);
    }

    /**
     * Prints badge certificate status box.
     * @return Either the status box html as a string or null
     */
    public function print_badgecert_status_box(badge_certificate $cert) {
        if (has_capability('moodle/badges:configurecertificate', $cert->get_context())) {
            $table = new html_table();
            $table->attributes['class'] = 'boxaligncenter statustable';

            $status = get_string('statuscertmessage_' . $cert->status, 'local_badgecerts');
            if ($cert->is_active()) {
                if ($cert->status != CERT_STATUS_ACTIVE_LOCKED) {
                    $action = $this->output->single_button(new moodle_url('/local/badgecerts/action.php',
                            array('id' => $cert->id, 'lock' => 1, 'sesskey' => sesskey(),
                        'return' => $this->page->url->out_as_local_url(false))),
                            get_string('deactivate', 'local_badgecerts'), 'POST', array('class' => 'activatebadge'));
                } else {
                    $action = null;
                }
            } else {
                $action = $this->output->single_button(new moodle_url('/local/badgecerts/action.php',
                        array('id' => $cert->id, 'activate' => 1, 'sesskey' => sesskey(),
                    'return' => $this->page->url->out_as_local_url(false))), get_string('activate', 'local_badgecerts'),
                        'POST', array('class' => 'activatebadge'));
            }
            $row = array($status, $action);
            $table->data[] = $row;

            $style = $cert->is_active() ? 'generalbox statusbox active' : 'generalbox statusbox inactive';
            return $this->output->box(html_writer::table($table), $style);
        }

        return null;
    }

    // Outputs table of user badge certificates.
    protected function render_cert_user_collection(cert_user_collection $certs) {
        global $CFG, $USER, $SITE;

        $paging = new paging_bar($certs->totalcount, $certs->page, $certs->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);

        // Search box.
        $searchform = $this->output->box($this->helper_search_form($certs->search), 'boxwidthwide boxaligncenter');

        // Local badge certificates.
        $localhtml = html_writer::start_tag('fieldset', array('id' => 'issued-badge-table', 'class' => 'generalbox'));
        $heading = get_string('localbadgecerts', 'local_badgecerts',
                format_string($SITE->fullname, true, array('context' => context_system::instance())));
        $localhtml .= html_writer::tag('legend',
                        $this->output->heading_with_help($heading, 'localbadgecertsh', 'local_badgecerts'));
        if ($certs->certs) {
            $table = new html_table();
            $table->attributes['class'] = 'statustable';
            $table->data[] = array($this->output->heading(get_string('badgecertsearned', 'local_badgecerts',
                                $certs->totalcount), 4, 'activatebadge'));
            $subheading = html_writer::table($table);

            $htmllist = $this->print_badgecerts_list($certs->certs, $USER->id);
            $localhtml .= $subheading . $searchform . $htmlpagingbar . $htmllist . $htmlpagingbar;
        } else {
            $localhtml .= $searchform . $this->output->notification(get_string('nobadgecertificates', 'local_badgecerts'));
        }
        $localhtml .= html_writer::end_tag('fieldset');

        return $localhtml;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Helper methods
    // Reused from stamps collection plugin
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders a text with icons to sort by the given column
     *
     * This is intended for table headings.
     *
     * @param string $text    The heading text
     * @param string $sortid  The column id used for sorting
     * @param string $sortby  Currently sorted by (column id)
     * @param string $sorthow Currently sorted how (ASC|DESC)
     *
     * @return string
     */
    protected function helper_sortable_heading($text, $sortid = null, $sortby = null, $sorthow = null) {
        $out = html_writer::tag('span', $text, array('class' => 'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid || $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'ASC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_asc', get_string('sortbyx', 'core', s($text)), null,
                        array('class' => 'iconsort')));
            }
            if ($sortby !== $sortid || $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'DESC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_desc', get_string('sortbyxreverse', 'core', s($text)), null,
                        array('class' => 'iconsort')));
            }
        }
        return $out;
    }

    /**
     * Tries to guess the fullname format set at the site
     *
     * @return string fl|lf
     */
    protected function helper_fullname_format() {
        $fake = new stdClass();
        $fake->lastname = 'LLLL';
        $fake->firstname = 'FFFF';
        $fullname = get_string('fullnamedisplay', '', $fake);
        if (strpos($fullname, 'LLLL') < strpos($fullname, 'FFFF')) {
            return 'lf';
        } else {
            return 'fl';
        }
    }

    /**
     * Renders a search form
     *
     * @param string $search Search string
     * @return string HTML
     */
    protected function helper_search_form($search) {
        global $CFG;
        require_once($CFG->libdir . '/formslib.php');

        $mform = new MoodleQuickForm('searchform', 'POST', $this->page->url);

        $mform->addElement('hidden', 'sesskey', sesskey());

        $el[] = $mform->createElement('text', 'search', get_string('search'), array('size' => 20));
        $mform->setDefault('search', $search);
        $el[] = $mform->createElement('submit', 'submitsearch', get_string('search'));
        $el[] = $mform->createElement('submit', 'clearsearch', get_string('clear'));
        $mform->addGroup($el, 'searchgroup', get_string('searchname', 'badges'), ' ', false);

        ob_start();
        $mform->display();
        $out = ob_get_clean();

        return $out;
    }

}

/**
 * Collection of all badge certificates for view.php page
 */
class cert_collection implements renderable {

    /** @var string how are the data sorted */
    public $sort = 'name';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of badge certificates to display per page */
    public $perpage = CERT_PERPAGE;

    /** @var int the total number of badge certificates to display */
    public $totalcount = null;

    /** @var array list of badge certificates */
    public $certs = array();

    /**
     * Initializes the list of badge certificates to display
     *
     * @param array $certs Badge certificates to render
     */
    public function __construct($certs) {
        $this->certs = $certs;
    }

}

/**
 * Collection of badge certificates used at the index.php page
 */
class cert_management extends cert_collection implements renderable {
    
}

/**
 * Collection of user badge certificates used at the mycerts.php page
 */
class cert_user_collection extends cert_collection implements renderable {

    /** @var string search */
    public $search = '';

    /**
     * Initializes user badge certificate collection.
     *
     * @param array $certs Badge certificates to render
     * @param int $userid Badge certificates owner
     */
    public function __construct($certs, $userid) {
        global $CFG;
        parent::__construct($certs);
    }

}

/**
 * An issued badge certificates for mycerts.php page
 */
class issued_badgecert implements renderable {

    /** @var issued badge ID */
    public $id;

    /** @var issued badge */
    public $issued;

    /** @var badge recipient */
    public $recipient;

    /** @var badge class */
    public $badgeclass;

    /** @var badge visibility to others */
    public $visible = 0;

    /** @var badge class */
    public $badgeid = 0;

    /** @var badge certificate class */
    public $certid = 0;

    /**
     * Initializes the badge to display
     *
     * @param string $hash Issued badge hash
     */
    public function __construct($hash) {
        global $DB;

        $assertion = new core_badges_assertion($hash);
        $this->issued = $assertion->get_badge_assertion();
        $this->badgeclass = $assertion->get_badge_class();

        $rec = $DB->get_record_sql('SELECT id, userid, visible, badgeid
                FROM {badge_issued}
                WHERE ' . $DB->sql_compare_text('uniquehash', 40) . ' = ' . $DB->sql_compare_text(':hash', 40),
                array('hash' => $hash), IGNORE_MISSING);
        if ($rec) {
            // Get a recipient from database.
            $namefields = get_all_user_name_fields(true, 'u');
            $user = $DB->get_record_sql("SELECT u.id, $namefields, u.deleted,
                                                u.email AS accountemail, b.email AS backpackemail
                        FROM {user} u LEFT JOIN {badge_backpack} b ON u.id = b.userid
                        WHERE u.id = :userid", array('userid' => $rec->userid));
            // Add custom profile field 'Datumrojstva' value
            $fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => 'Datumrojstva'));
            if ($fieldid && $birthdate = $DB->get_field('user_info_data', 'data',
                    array('userid' => $rec->userid, 'fieldid' => $fieldid))) {
                $user->birthdate = $birthdate;
            } else {
                $user->birthdate = null;
            }
            // Add custom profile field 'VIZ' value
            $fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => 'VIZ'));
            if ($fieldid && $institution = $DB->get_field('user_info_data', 'data',
                    array('userid' => $rec->userid, 'fieldid' => $fieldid))) {
                $user->institution = $institution;
            } else {
                $user->institution = null;
            }

            $this->recipient = $user;
            $this->id = $rec->id;
            $this->visible = $rec->visible;
            $this->badgeid = $rec->badgeid;
            // Get badge certificate for this badge from database.
            $certid = $DB->get_field('badge', 'certid', array('id' => $rec->badgeid));
            $this->certid = $certid;
        }
    }

    /**
     * Generates badge certificate in PDF format.
     */
    public function generate_badge_certificate() {
        global $CFG, $DB, $USER;
        require_once($CFG->libdir . '/tcpdf/tcpdf.php');

        $cert = new badge_certificate($this->certid);
        $pdf = new TCPDF($cert->orientation, $cert->unit, $cert->format, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // override default margins
        $pdf->SetMargins(0, 0, 0, true);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Add badge certificate background image
        if ($cert->certbgimage) {
            // Add a page
            // This method has several options, check the source code documentation for more information.
            $pdf->AddPage();

            // get the current page break margin
            $break_margin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);

            $template = file_get_contents($cert->certbgimage, false);
            // Get booking related data
            $booking = new StdClass();
            $booking->title = get_string('titlenotset', 'local_badgecerts');
            $booking->startdate = get_string('datenotdefined', 'local_badgecerts');
            $booking->enddate = get_string('datenotdefined', 'local_badgecerts');
            $booking->duration = 0;

            if ($cert->bookingid > 0 && file_exists($CFG->dirroot . '/mod/booking/locallib.php')) {
                require_once($CFG->dirroot . '/mod/booking/locallib.php');
                $coursemodule = get_coursemodule_from_id('booking', $cert->bookingid);
                $bookingid = $coursemodule->instance;
                $optionid = booking_getbookingoptionid($bookingid, $USER->id);
                if (isset($optionid) && $optionid > 0) {
                    $options = booking_getbookingoptions($bookingid, $optionid);
                    // Set seminar title
                    if (isset($options['text']) && !empty($options['text'])) {
                        $booking->title = $options['text'];
                    }
                    // Set seminar start date
                    if (isset($options['coursestarttime']) && !empty($options['coursestarttime'])) {
                        $booking->startdate = userdate((int) $options['coursestarttime'], get_string('datetimeformat', 'local_badgecerts'));
                    }
                    // Set seminar end date
                    if (isset($options['courseendtime']) && !empty($options['courseendtime'])) {
                        $booking->enddate = userdate((int) $options['courseendtime'], get_string('datetimeformat', 'local_badgecerts'));
                    }
                    // Set seminar duration
                    if (isset($options['duration']) && !empty($options['duration'])) {
                        $booking->duration = $options['duration'];
                    }
                }
            }

            // Replace all placeholder tags
            $now = time();
            // Set account email if backpack email is not set up and/or connected
            if (isset($this->recipient->backpackemail) && !empty($this->recipient->backpackemail)) {
                $recipientemail = $this->recipient->backpackemail;
            } else {
                $recipientemail = $this->recipient->accountemail;
            }
            $placeholders = array(
                '[[recipient-fname]]', // Adds the recipient's first name
                '[[recipient-lname]]', // Adds the recipient's last name
                '[[recipient-flname]]', // Adds the recipient's full name (first, last)
                '[[recipient-lfname]]', // Adds the recipient's full name (last, first)
                '[[recipient-email]]', // Adds the recipient's email address
                '[[issuer-name]]', // Adds the issuer's name or title
                '[[issuer-contact]]', // Adds the issuer's contact information
                '[[badge-name]]', // Adds the badge's name or title
                '[[badge-desc]]', // Adds the badge's description
                '[[badge-number]]', // Adds the badge's ID number
                '[[badge-course]]', // Adds the name of the course where badge was awarded
                '[[badge-hash]]', // Adds the badge hash value
                '[[datetime-Y]]', // Adds the year
                '[[datetime-d.m.Y]]', // Adds the date in dd.mm.yyyy format
                '[[datetime-d/m/Y]]', // Adds the date in dd/mm/yyyy format
                '[[datetime-F]]', // Adds the date (used in DB datestamps)
                '[[datetime-s]]', // Adds Unix Epoch Time timestamp';
                '[[booking-title]]', // Adds the seminar title
                '[[booking-startdate]]', // Adds the seminar start date
                '[[booking-enddate]]', // Adds the seminar end date
                '[[booking-duration]]', // Adds the seminar duration
                '[[recipient-birthdate]]', // Adds the recipient's date of birth
                '[[recipient-institution]]', // Adds the institution where the recipient is employed
                '[[badge-date-issued]]', // Adds the date when badge was issued
            );
            $values = array(
                $this->recipient->firstname,
                $this->recipient->lastname,
                $this->recipient->firstname . ' ' . $this->recipient->lastname,
                $this->recipient->lastname . ' ' . $this->recipient->firstname,
                $recipientemail,
                $cert->issuername,
                $cert->issuercontact,
                $this->badgeclass['name'],
                $this->badgeclass['description'],
                $this->id,
                $DB->get_field('course', 'fullname', array('id' => $cert->courseid)),
                sha1(rand() . $cert->usercreated . $cert->id . $now),
                strftime('%Y', $now),
                userdate($now, get_string('datetimeformat', 'local_badgecerts')),
                userdate($now, get_string('datetimeformat', 'local_badgecerts')),
                strftime('%F', $now),
                strftime('%s', $now),
                $booking->title,
                $booking->startdate,
                $booking->enddate,
                $booking->duration,
                userdate((int) $this->recipient->birthdate, get_string('datetimeformat', 'local_badgecerts')),
                $this->recipient->institution,
                userdate((int) $this->issued['issuedOn'], get_string('datetimeformat', 'local_badgecerts')),
            );
            $template = str_replace($placeholders, $values, $template);

            $pdf->ImageSVG($file = '@' . $template, 0, 0, 0, 0, '', '', '', 0, true);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $break_margin);
            // set the starting point for the page content
            $pdf->setPageMark();
        }

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($this->badgeclass['name'] . '.pdf', 'D');
    }

}
