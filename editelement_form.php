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
 * Form classes for editing badge certificates.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

/**
 * Form to edit badge certificate elements.
 *
 */
class edit_cert_element_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        $mform = $this->_form;
        $element = (isset($this->_customdata['certificateelement'])) ? $this->_customdata['certificateelement'] : false;
        $action = $this->_customdata['action'];

        $mform->addElement('header', 'badgecertificateelement', get_string('badgecertificateelement', 'local_badgecerts'));
        $mform->addElement('text', 'rawtext', get_string('rawtext', 'local_badgecerts'), array('size' => '70'));
        $mform->addHelpButton('rawtext', 'rawtext', 'local_badgecerts');
        $mform->setType('rawtext', PARAM_CLEANHTML);
        $mform->addRule('rawtext', null, 'required');
        $mform->addRule('rawtext', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('text', 'x', get_string('elementposition:x', 'local_badgecerts'), array('size' => '4'));
        $mform->setType('x', PARAM_INT);
        $mform->setDefault('x', 0);
        $mform->addRule('x', null, 'required');
        $mform->addRule('x', null, 'maxlength', 4);

        $mform->addElement('text', 'y', get_string('elementposition:y', 'local_badgecerts'), array('size' => '4'));
        $mform->setType('y', PARAM_INT);
        $mform->setDefault('y', 0);
        $mform->addRule('y', null, 'required');
        $mform->addRule('y', null, 'maxlength', 4);

        $mform->addElement('text', 'size', get_string('elementsize', 'local_badgecerts'), array('size' => '3'));
        $mform->setType('size', PARAM_INT);
        $mform->setDefault('size', 12);
        $mform->addRule('size', null, 'required');
        $mform->addRule('size', null, 'maxlength', 3);

        $familyoptions = array(
            'freesans'    => get_string('elementfamily:freesans', 'local_badgecerts'),
            'freeserif'   => get_string('elementfamily:freeserif', 'local_badgecerts'),
        );
        $mform->addElement('select', 'family', get_string('elementfamily', 'local_badgecerts'), $familyoptions);
        $mform->addRule('family', null, 'required');
        $mform->setDefault('family', 'freesans');

        $alignoptions = array(
            'L'      => get_string('elementalign:L', 'local_badgecerts'),
            'C'      => get_string('elementalign:C', 'local_badgecerts'),
            'R'      => get_string('elementalign:R', 'local_badgecerts'),
            'I'      => get_string('elementalign:I', 'local_badgecerts'), // Invert
            'T'      => get_string('elementalign:T', 'local_badgecerts'), // Top down
            'B'      => get_string('elementalign:B', 'local_badgecerts'), // Bottom up.
            ''      => get_string('elementalign:0', 'local_badgecerts'),
        );
        $mform->addElement('select', 'align', get_string('elementalign', 'local_badgecerts'), $alignoptions);
        $mform->setDefault('align', '');

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        if ($action == 'new') {
            $this->add_action_buttons(true, get_string('createcertelmbutton', 'local_badgecerts'));
        } else {
            // Add hidden fields.
            $mform->addElement('hidden', 'id', $element->id);
            $mform->setType('id', PARAM_INT);

            $this->add_action_buttons();
            $this->set_data($element);

            // Freeze all elements if badge certificate is active or locked.
            if ($element->is_active() || $element->is_locked()) {
                $mform->hardFreezeAllVisibleExcept(array());
            }
        }
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass|array $defaultvalues object or array of default values
     */
    public function set_data($element) {
        $defaultvalues = array();
        parent::set_data($element);

        parent::set_data($defaultvalues);
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
