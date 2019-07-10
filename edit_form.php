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
 * Form classes for editing badge certificates
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Gregor Anželj <gregor.anzelj@gmail.com>
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

/**
 * Form to edit badge certificate details.
 *
 */
class edit_cert_details_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $CFG, $PAGE, $DB;

        $mform = $this->_form;
        $cert = (isset($this->_customdata['badgecertificate'])) ? $this->_customdata['badgecertificate'] : false;
        $action = $this->_customdata['action'];

        $mform->addElement('header', 'badgecertificatedetails', get_string('badgecertificatedetails', 'local_badgecerts'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '70'));
        // Using PARAM_FILE to avoid problems later when downloading badge certificate files.
        $mform->setType('name', PARAM_FILE);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('textarea', 'description', get_string('description', 'local_badgecerts'),
            'wrap="virtual" rows="8" cols="70"');
        $mform->setType('description', PARAM_CLEANHTML);
        $mform->addRule('description', null, 'required');

        if (has_capability('local/badgecerts:assignofficialcertificate', $PAGE->context)) {
            $mform->addElement('checkbox', 'official', get_string('officialtype', 'local_badgecerts'),
                get_string('officialtypedesc', 'local_badgecerts'));
            $mform->setDefault('official', 0);
        } else {
            $mform->addElement('hidden', 'official', 0);
            $mform->setType('official', PARAM_INT);
        }

        $badges = array();

        if (!empty($this->_customdata['courseid'])) {
            $sql = 'SELECT b.id, b.name FROM {badge} b WHERE b.courseid = :courseid AND ' .
                '(b.status = 1 OR b.status = 3) ORDER BY b.name ASC';
            $params = array();
            $params['courseid'] = $this->_customdata['courseid'];

            $badges = $DB->get_records_sql_menu($sql, $params);
        }

        $mform->addElement('select', 'certid', get_string('certid', 'local_badgecerts'), $badges);

        $formatoptions = array(
            'A3' => get_string('certificateformat:A3', 'local_badgecerts'),
            'A4' => get_string('certificateformat:A4', 'local_badgecerts'),
            'B4' => get_string('certificateformat:B4', 'local_badgecerts'),
            'B5' => get_string('certificateformat:B5', 'local_badgecerts'),
            'LEGAL' => get_string('certificateformat:Legal', 'local_badgecerts'),
            'LETTER' => get_string('certificateformat:Letter', 'local_badgecerts'),
            'TABLOID' => get_string('certificateformat:Tabloid', 'local_badgecerts'),
        );
        $mform->addElement('select', 'format', get_string('certificateformat', 'local_badgecerts'), $formatoptions);
        $mform->setDefault('format', 'A4');
        $mform->addRule('format', null, 'required');

        $orientationoptions = array();
        $orientationoptions[] = & $mform->createElement('radio', 'orientation', '',
            get_string('certificateorientation:portrait', 'local_badgecerts'), 'P');
        $orientationoptions[] = & $mform->createElement('static', 'portrait_break', null, '<br/>');
        $orientationoptions[] = & $mform->createElement('radio', 'orientation', '',
            get_string('certificateorientation:landscape', 'local_badgecerts'), 'L');
        $mform->addGroup($orientationoptions, 'orientationgr',
            get_string('certificateorientation', 'local_badgecerts'), array(' '), false);
        $mform->setDefault('orientation', 'P');
        $mform->addRule('orientationgr', null, 'required');

        $unitoptions = array();
        $unitoptions[] = & $mform->createElement('radio', 'unit', '', get_string('certificateunit:pt', 'local_badgecerts'), 'pt');
        $unitoptions[] = & $mform->createElement('static', 'pt_break', null, '<br/>');
        $unitoptions[] = & $mform->createElement('radio', 'unit', '', get_string('certificateunit:mm', 'local_badgecerts'), 'mm');
        $unitoptions[] = & $mform->createElement('static', 'mm_break', null, '<br/>');
        $unitoptions[] = & $mform->createElement('radio', 'unit', '', get_string('certificateunit:cm', 'local_badgecerts'), 'cm');
        $unitoptions[] = & $mform->createElement('static', 'cm_break', null, '<br/>');
        $unitoptions[] = & $mform->createElement('radio', 'unit', '', get_string('certificateunit:in', 'local_badgecerts'), 'in');
        $mform->addGroup($unitoptions, 'unitgr', get_string('certificateunit', 'local_badgecerts'), array(' '), false);
        $mform->setDefault('unit', 'mm');
        $mform->addRule('unitgr', null, 'required');

        $certtypes = array();
        $certtypes[0] = get_string('certificateforbadge', 'local_badgecerts');
        $certtypes[1] = get_string('certificateformodbookingusers', 'local_badgecerts');
        $certtypes[4] = get_string('certificateformodbookinguserssum', 'local_badgecerts');
        $certtypes[2] = get_string('certificateformodbookingteachers', 'local_badgecerts');
        $certtypes[3] = get_string('certificateforquizgrading', 'local_badgecerts');

        $mform->addElement('select', 'certtype', get_string('certificatefor', 'local_badgecerts'), $certtypes);
        $mform->setDefault('certtype', 0);

        $imageoptions = array('maxbytes' => 262144, 'accepted_types' => array('.svg'));
        $mform->addElement('filepicker', 'certbgimage', get_string('backgroundimage', 'local_badgecerts'), null, $imageoptions);
        $mform->addHelpButton('certbgimage', 'backgroundimage', 'local_badgecerts');
        if (isset($cert->certbgimage) && !empty($cert->certbgimage)) {
            // Display which SVG template was uploaded.
            $mform->addElement('static', 'currentbgimage', get_string('currentimage', 'local_badgecerts'), $cert->certbgimage);
        } else {
            // New badge certificate form - require SVG template!
            $mform->addRule('certbgimage', null, 'required');
        }

        $mform->addElement('text', 'bookingid', get_string('bookingid', 'local_badgecerts'), array('size' => '10'));
        $mform->setType('bookingid', PARAM_INT);
        $mform->addHelpButton('bookingid', 'bookingid', 'local_badgecerts');

        $mform->addElement('text', 'quizgradingid', get_string('quizgradingid', 'local_badgecerts'), array('size' => '10'));
        $mform->setType('quizgradingid', PARAM_INT);
        $mform->addHelpButton('quizgradingid', 'quizgradingid', 'local_badgecerts');

        $mform->addElement('header', 'issuerdetails', get_string('issuerdetails', 'local_badgecerts'));

        $mform->addElement('text', 'issuername', get_string('issuername', 'local_badgecerts'), array('size' => '70'));
        $mform->setType('issuername', PARAM_NOTAGS);
        $mform->addRule('issuername', null, 'required');
        if (isset($CFG->badges_defaultissuername)) {
            $mform->setDefault('issuername', $CFG->badges_defaultissuername);
        }
        $mform->addHelpButton('issuername', 'issuername', 'local_badgecerts');

        $mform->addElement('text', 'issuercontact', get_string('contact', 'local_badgecerts'), array('size' => '70'));
        if (isset($CFG->badges_defaultissuercontact)) {
            $mform->setDefault('issuercontact', $CFG->badges_defaultissuercontact);
        }
        $mform->setType('issuercontact', PARAM_RAW);
        $mform->addHelpButton('issuercontact', 'contact', 'local_badgecerts');

        $mform->addElement('header', 'datelimit', get_string('datelimit', 'local_badgecerts'));

        $mform->addElement('static', 'whenisthisfiltervalid', '', get_string('whenisthisfiltervalid', 'local_badgecerts'));

        $mform->addElement('checkbox', 'restricttocertaindate', get_string('usestartandenddate', 'local_badgecerts'));
        $mform->disabledIf('restricttocertaindate', 'certtype', 'in', array(0, 3));

        $mform->addElement('date_time_selector', 'startdate', get_string("starttime", "local_badgecerts"));
        $mform->setType('startdate', PARAM_INT);
        $mform->disabledIf('startdate', 'restricttocertaindate', 'notchecked');

        $mform->addElement('date_time_selector', 'enddate', get_string("endtime", "local_badgecerts"));
        $mform->setType('enddate', PARAM_INT);
        $mform->disabledIf('enddate', 'restricttocertaindate', 'notchecked');

        $mform->addElement('header', 'qrcode', get_string('qrcode', 'local_badgecerts'));

        $mform->addElement('checkbox', 'qrshow', get_string('qrshow', 'local_badgecerts'));
        $mform->setType('qrshow', PARAM_INT);

        $mform->addElement('text', 'qrx', get_string('qrx', 'local_badgecerts'), array('size' => '10'));
        $mform->setType('qrx', PARAM_INT);

        $mform->addElement('text', 'qry', get_string('qry', 'local_badgecerts'), array('size' => '10'));
        $mform->setType('qry', PARAM_INT);

        $mform->addElement('text', 'qrw', get_string('qrw', 'local_badgecerts'), array('size' => '10'));
        $mform->setType('qrw', PARAM_INT);

        $mform->addElement('text', 'qrh', get_string('qrh', 'local_badgecerts'), array('size' => '10'));
        $mform->setType('qrh', PARAM_INT);

        $mform->addElement('select', 'qrdata', get_string('qrdata', 'local_badgecerts'), array(
            0 => get_string('userid', 'local_badgecerts'), 1 => get_string('username', 'local_badgecerts')));
        $mform->setType('qrdata', PARAM_INT);

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        if ($action == 'new') {
            $this->add_action_buttons(true, get_string('createcertbutton', 'local_badgecerts'));
        } else {
            // Add hidden fields.
            $mform->addElement('hidden', 'id', $cert->id);
            $mform->setType('id', PARAM_INT);

            $this->add_action_buttons();
            $this->set_data($cert);

            // Freeze all elements if badge certificate is active or locked.
            if ($cert->is_active() || $cert->is_locked()) {
                $mform->hardFreezeAllVisibleExcept(array());
            }
        }
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass|array $defaultvalues object or array of default values
     */
    public function set_data($cert) {
        $defaultvalues = array();
        parent::set_data($cert);

        $defaultvalues['currentimage'] = $cert->certbgimage;
        if ($cert->startdate != 0) {
            $defaultvalues['restricttocertaindate'] = 'checked';
        }

        parent::set_data($defaultvalues);
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if (!empty($data['issuercontact']) && !validate_email($data['issuercontact'])) {
            $errors['issuercontact'] = get_string('invalidemail');
        }

        // Check for duplicate badge certificate names.
        if ($data['action'] == 'new') {
            $duplicate = $DB->record_exists_select('local_badgecerts', 'name = :name', array('name' => $data['name']));
        } else {
            $duplicate = $DB->record_exists_select('local_badgecerts', 'name = :name AND id != :certid',
                array('name' => $data['name'], 'certid' => $data['id']));
        }

        if ($duplicate) {
            $errors['name'] = get_string('error:duplicatecertname', 'local_badgecerts');
        }

        return $errors;
    }

}
