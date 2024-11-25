<?php

namespace local_badgecerts\event;

class not_suported extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'local_badgecerts';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('triedmigrate', 'local_badgecerts');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' tried to migrate certificates to tool certificates. This migration is not suported. Only certificate for booking users is supported!";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/local/badgecerts/view.php', array('id' => $this->objectid));
    }
}