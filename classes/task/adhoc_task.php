<?php

namespace local_badgecerts\task;

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

defined('MOODLE_INTERNAL') || die();

class adhoc_task extends \core\task\adhoc_task {
    // Execute the task.
    public function execute() {
        // Get custom data passed to the task.
        $data = $this->get_custom_data();

        $cert = new \badge_certificate($data->id);
        $cert->migrate($data->certificate);
    }
}
