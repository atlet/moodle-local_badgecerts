<?php
require_once("$CFG->libdir/formslib.php");

class migrate_form extends moodleform {
    // Define the form elements.
    protected function definition() {
        $mform = $this->_form;

        $context = $this->_customdata['context'];

        $options = [
            '' => get_string('choosedots'), // Default "Choose..." option.
        ];
        if (!empty($records = \tool_certificate\permission::get_visible_templates($context))) {
            foreach ($records as $record) {
                $options[$record->id] = format_string($record->name);
            }
        }

        $mform->addElement('select', 'certificate', get_string('selectcertificate', 'local_badgecerts'), $options);
        $mform->setType('certificate', PARAM_TEXT);
        $mform->addRule('certificate', null, 'required', null, 'client');

        // Add submit and cancel buttons.
        $this->add_action_buttons();
    }
}