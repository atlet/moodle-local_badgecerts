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
 * Renderer for use with the badge certificates output.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

/**
 * Standard HTML output renderer for badge certificates
 */
class local_badgecerts_renderer extends plugin_renderer_base {

    /**
     * Outputs badge certificates list.
     *
     * @param integer $badges  Badge id.
     * @param integer $userid  User ID.
     * @param boolean $profile Show profile.
     *
     * @return string Html view.
     */
    public function print_badgecerts_list($badges, $userid, $profile = false) {
        global $USER;
        foreach ($badges as $badge) {
            $context = ($badge->type == CERT_TYPE_SITE) ? context_system::instance() : context_course::instance($badge->courseid);

            $imageurl = moodle_url::make_pluginfile_url(
                $context->id,
                'badges',
                'badgeimage',
                $badge->badgeid,
                '/',
                'f1',
                false
            );

            $name = html_writer::tag('span', $badge->name, array('class' => 'badge-name'));

            $image = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => 'badge-image'));
            if (!empty($badge->dateexpire) && $badge->dateexpire < time()) {
                $image .= $this->output->pix_icon(
                    'i/expired',
                    get_string('expireddate', 'badges', userdate($badge->dateexpire)),
                    'moodle',
                    array('class' => 'expireimage')
                );
                $name .= '(' . get_string('expired', 'badges') . ')';
            }

            if (!$profile) {
                $burl = new moodle_url('/badges/badge.php', array('hash' => $badge->uniquehash));
            } else {
                $hash = hash('md5', $badge->hostedUrl);
                $burl = new moodle_url('/badges/external.php', array('hash' => $hash, 'user' => $userid));
            }

            $badgeview = $status = $push = '';
            if (($userid == $USER->id) && !$profile) {
                $url = new moodle_url(
                    'mycerts.php',
                    array('download' => $badge->id, 'hash' => $badge->uniquehash, 'sesskey' => sesskey())
                );
                $badgeview = $this->output->action_icon($burl, new pix_icon('i/info', get_string('info')));
                $badgeview .= $this->output->action_icon($url, new pix_icon('a/download_all', get_string('download')));

                if (in_array($badge->certtype, array(1, 2, 4)) && $badge->bookingid > 0) {
                    $bookingurl = new moodle_url('/mod/booking/view.php', array('id' => $badge->bookingid));
                    $badgeview .= $this->output->action_icon($bookingurl, new pix_icon('i/grades', get_string('booking', 'local_badgecerts')));
                }
            }

            $actions = html_writer::tag('div', $push . $badgeview . $status, array('class' => 'badge-actions'));
            $items[] = html_writer::link($url, $image . $actions . $name, array('title' => $badge->name));
        }

        return html_writer::alist($items, array('class' => 'badges'));
    }

    /**
     * Booo, does nothing.
     *
     * @return string Booooo.
     */
    public function print_badgecert_view() {
        $display = "";

        return $display;
    }

    /**
     * Prints a badge certificate overview infomation.
     *
     * @param integer $cert Certificate id.
     *
     * @return string Html view.
     */
    public function print_badgecert_overview($cert) {
        $display = "";

        // Badge certificate details.
        $display .= html_writer::start_tag('fieldset', array('class' => 'generalbox'));
        $display .= html_writer::tag(
            'legend',
            get_string('badgecertificatedetails', 'local_badgecerts'),
            array('class' => 'bold')
        );

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
        $issuertable->data[] = array(
            get_string('contact', 'local_badgecerts') . ":",
            html_writer::tag('a', $cert->issuercontact, array('href' => 'mailto:' . $cert->issuercontact))
        );
        $display .= html_writer::table($issuertable);
        $display .= html_writer::end_tag('fieldset');

        return $display;
    }

    /**
     * Prints action icons for the badge certificate.
     *
     * @param integer $cert    Certificate ID.
     * @param context $context Context.
     *
     * @return string Html.
     */
    public function print_cert_table_actions($cert, $context) {
        $actions = "";

        if (has_capability('local/badgecerts:configurecertificate', $context)) {
            // Activate/deactivate badge certificate.
            if ($cert->status == CERT_STATUS_INACTIVE || $cert->status == CERT_STATUS_INACTIVE_LOCKED) {
                // Activate will go to another page and ask for confirmation.
                $url = new moodle_url('/local/badgecerts/action.php');
                $url->param('id', $cert->id);
                $url->param('activate', true);
                $url->param('sesskey', sesskey());
                $return = new moodle_url(qualified_me());
                $url->param('return', $return->out_as_local_url(false));
                $actions .= $this->output->action_icon(
                    $url,
                    new pix_icon('t/show', get_string('activate', 'local_badgecerts'))
                ) . " ";
            } else {
                $url = new moodle_url(qualified_me());
                $url->param('lock', $cert->id);
                $url->param('sesskey', sesskey());
                $actions .= $this->output->action_icon(
                    $url,
                    new pix_icon('t/hide', get_string('deactivate', 'local_badgecerts'))
                ) . " ";
            }
        }

        // Preview badge certificate.
        if (has_capability('local/badgecerts:configurecertificate', $context)) {
            $url = new moodle_url(
                '/local/badgecerts/action.php',
                array('preview' => '1', 'id' => $cert->id, 'sesskey' => sesskey())
            );
            $actions .= $this->output->action_icon($url, new pix_icon('t/preview', get_string('preview'))) . " ";
        }

        // Edit badge certificate.
        if (has_capability('local/badgecerts:configurecertificate', $context)) {
            $url = new moodle_url('/local/badgecerts/edit.php', array('id' => $cert->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Delete badge certificate.
        if (has_capability('local/badgecerts:deletecertificate', $context)) {
            $url = new moodle_url(qualified_me());
            $url->param('delete', $cert->id);
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
        }

        return $actions;
    }

    /**
     * Outputs table of badge certificates with actions available.
     *
     * @param cert_management $certs Certificate object.
     *
     * @return string Html view.
     */
    protected function render_cert_management(cert_management $certs) {
        $paging = new paging_bar($certs->totalcount, $certs->page, $certs->perpage, $this->page->url, 'page');

        // New badge certificate button.
        $htmlnew = '';
        if (has_capability('local/badgecerts:createcertificate', $this->page->context)) {
            $n['type'] = $this->page->url->get_param('type');
            $n['id'] = $this->page->url->get_param('id');
            $htmlnew = $this->output->single_button(
                new moodle_url('/local/badgecerts/new.php', $n),
                get_string('newbadgecertificate', 'local_badgecerts')
            );
        }

        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

        $sortbyname = $this->helper_sortable_heading(get_string('name'), 'name', $certs->sort, $certs->dir);
        $sortbyissuer = $this->helper_sortable_heading(
            get_string('issuername', 'local_badgecerts'),
            'issuer',
            $certs->sort,
            $certs->dir
        );
        $sortbystatus = $this->helper_sortable_heading(
            get_string('status', 'badges'),
            'status',
            $certs->sort,
            $certs->dir
        );
        $table->head = array(
            $sortbyname,
            $sortbyissuer,
            $sortbystatus,
            get_string('actions')
        );
        $table->colclasses = array('name', 'status', 'criteria', 'actions');

        foreach ($certs->certs as $c) {
            $style = !$c->is_active() ? array('class' => 'dimmed') : array();

            if (!$c->is_active() && !has_capability('local/badgecerts:createcertificate', $this->page->context)) {
                $name = $c->name;
            } else {
                $name = html_writer::link(
                    new moodle_url('/local/badgecerts/view.php', array('id' => $c->id)),
                    $c->name,
                    $style
                );
            }
            $issuer = $c->issuername;
            $status = $c->statstring;

            $actions = self::print_cert_table_actions($c, $this->page->context);

            $row = array($name, $issuer, $status, $actions);
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlnew . $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    /**
     * Prints tabs for badge certificate editing.
     *
     * @param int $certid Certificate id.
     * @param context $context Context.
     * @param string $current Which tab to show.
     *
     * @return string Html view.
     */
    public function print_badgecert_tabs($certid, $context, $current = 'overview') {
        global $OUTPUT;

        // Output button => back to index page.
        $cert = new badge_certificate($certid);
        $n = array('type' => $cert->type, 'id' => $cert->courseid);
        $caption = get_string('managebadgecertificates', 'local_badgecerts');        

        $button_left = new single_button(new moodle_url('/local/badgecerts/index.php', $n), $caption, 'get');        

        $button_right_html = '';
        if ($current == 'view') {
            $button_right = new single_button(new moodle_url('/local/badgecerts/migrate.php', ['id' => $certid]), get_string('migrate', 'local_badgecerts'), 'get');
            $button_right_html = html_writer::div($OUTPUT->render($button_right), 'right-button');
        }

        $button_left_html = html_writer::div($OUTPUT->render($button_left), 'left-button');

        // Output the buttons within a flex container.
        echo html_writer::div($button_left_html . $button_right_html, 'button-container');

        // Output tabs.
        $row = array();

        $row[] = new tabobject(
            'overview',
            new moodle_url('/local/badgecerts/overview.php', array('id' => $certid)),
            get_string('boverview', 'local_badgecerts')
        );

        if ((has_capability('local/badgecerts:configurecertificate', $context) && $cert->official == '0') ||
            (has_any_capability(array('moodle/role:manage'), $context))
        ) {
            $row[] = new tabobject(
                'details',
                new moodle_url('/local/badgecerts/edit.php', array('id' => $certid)),
                get_string('bdetails', 'local_badgecerts')
            );
        }

        if (has_capability('local/badgecerts:viewcertificates', $context)) {
            $row[] = new tabobject(
                'view',
                new moodle_url('/local/badgecerts/view.php', array('id' => $certid)),
                get_string('viewcertificates', 'local_badgecerts')
            );
        }

        echo $this->tabtree($row, $current);
    }

    /**
     * Show filter box.
     *
     * @param badge_certificate $cert     Certificate object.
     * @param string            $url      Url.
     * @param integer           $day      Day.
     * @param integer           $month    Month.
     * @param integer           $year     Year.
     * @param integer           $dayend   Day.
     * @param integer           $monthend Month.
     * @param integer           $yearend  Year.

     * @return void
     */
    public function print_badgecert_filter_box(
        badge_certificate $cert,
        $url,
        $day = 0,
        $month = 0,
        $year = 0,
        $dayend = 0,
        $monthend = 0,
        $yearend = 0
    ) {

        $ctime = 0;
        $ctimeend = 0;

        if ($year > 0) {
            $ctime = mktime(0, 0, 0, $month, $day, $year);
            $ctimeend = mktime(0, 0, 0, $monthend, $dayend, $yearend);
        }

        echo html_writer::start_tag(
            'form',
            array('class' => 'reportbadgesselecform', 'action' => $url, 'method' => 'get')
        );

        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $cert->id));

        echo "<div class=\"form-inline\">";
        echo html_writer::select_time('days', 'day', $ctime);
        echo html_writer::select_time('months', 'month', $ctime);
        echo html_writer::select_time('years', 'year', $ctime);
        echo "&nbsp;-&nbsp;";
        echo html_writer::select_time('days', 'dayend', $ctimeend);
        echo html_writer::select_time('months', 'monthend', $ctimeend);
        echo html_writer::select_time('years', 'yearend', $ctimeend);
        echo "&nbsp;";
        echo html_writer::empty_tag(
            'input',
            array('class' => 'btn btn-primary', 'type' => 'submit', 'value' => get_string('filterreport', 'local_badgecerts'))
        );
        echo "&nbsp;";
        echo html_writer::empty_tag(
            'input',
            array(
                'class' => 'btn btn-secondary',
                'id' => "buttonclear",
                'type' => 'button',
                'value' => get_string('reset', 'local_badgecerts')
            )
        );
        echo "</div>";
        echo html_writer::end_tag('form');
    }

    /**
     * Prints badge certificate status box.
     *
     * @param badge_certificate $cert Badge certificate object.
     *
     * @return Either the status box html as a string or null
     */
    public function print_badgecert_status_box(badge_certificate $cert) {
        if (has_capability('local/badgecerts:configurecertificate', $cert->get_context())) {
            $table = new html_table();
            $table->attributes['class'] = 'boxaligncenter statustable';

            $status = get_string('statuscertmessage_' . $cert->status, 'local_badgecerts');
            if ($cert->is_active()) {
                if ($cert->status != CERT_STATUS_ACTIVE_LOCKED) {
                    $action = $this->output->single_button(
                        new moodle_url(
                            '/local/badgecerts/action.php',
                            array(
                                'id' => $cert->id,
                                'lock' => 1,
                                'sesskey' => sesskey(),
                                'return' => $this->page->url->out_as_local_url(false)
                            )
                        ),
                        get_string('deactivate', 'local_badgecerts'),
                        'POST',
                        array('class' => 'activatebadge')
                    );
                } else {
                    $action = null;
                }
            } else {
                $action = $this->output->single_button(
                    new moodle_url(
                        '/local/badgecerts/action.php',
                        array(
                            'id' => $cert->id,
                            'activate' => 1,
                            'sesskey' => sesskey(),
                            'return' => $this->page->url->out_as_local_url(false)
                        )
                    ),
                    get_string('activate', 'local_badgecerts'),
                    'POST',
                    array('class' => 'activatebadge')
                );
            }
            $row = array($status, $action);
            $table->data[] = $row;

            $style = $cert->is_active() ? 'generalbox statusbox active' : 'generalbox statusbox inactive';
            return $this->output->box(html_writer::table($table), $style);
        }

        return null;
    }

    /**
     * Outputs table of user badge certificates.
     *
     * @param cert_user_collection $certs Cert object.
     *
     * @return string Html string.
     */
    protected function render_cert_user_collection(cert_user_collection $certs) {
        global $USER, $SITE;

        $paging = new paging_bar($certs->totalcount, $certs->page, $certs->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);

        // Search box.
        $searchform = $this->output->box($this->helper_search_form($certs->search), 'boxwidthwide boxaligncenter');

        // Local badge certificates.
        $localhtml = html_writer::start_tag('fieldset', array('id' => 'issued-badge-table', 'class' => 'generalbox'));
        $heading = get_string(
            'localbadgecerts',
            'local_badgecerts',
            format_string($SITE->fullname, true, array('context' => context_system::instance()))
        );
        $localhtml .= html_writer::tag(
            'legend',
            $this->output->heading_with_help($heading, 'localbadgecertsh', 'local_badgecerts')
        );
        if ($certs->certs) {
            $table = new html_table();
            $table->attributes['class'] = 'statustable';
            $table->data[] = array($this->output->heading(get_string(
                'badgecertsearned',
                'local_badgecerts',
                $certs->totalcount
            ), 4, 'activatebadge'));
            $subheading = html_writer::table($table);

            $htmllist = $this->print_badgecerts_list($certs->certs, $USER->id);
            $localhtml .= $subheading . $searchform . $htmlpagingbar . $htmllist . $htmlpagingbar;
        } else {
            $localhtml .= $searchform . $this->output->notification(get_string('nobadgecertificates', 'local_badgecerts'));
        }
        $localhtml .= html_writer::end_tag('fieldset');

        return $localhtml;
    }

    /**
     * Helper methods.
     * Reused from stamps collection plugin.
     *
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
                $out .= $this->output->action_icon(
                    $url,
                    new pix_icon(
                        't/sort_asc',
                        get_string('sortbyx', 'core', s($text)),
                        null,
                        array('class' => 'iconsort')
                    )
                );
            }
            if ($sortby !== $sortid || $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'DESC'));
                $out .= $this->output->action_icon(
                    $url,
                    new pix_icon(
                        't/sort_desc',
                        get_string('sortbyxreverse', 'core', s($text)),
                        null,
                        array('class' => 'iconsort')
                    )
                );
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
        parent::__construct($certs);
    }
}
