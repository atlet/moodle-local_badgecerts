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
 * Shedule a task.
 *
 * @package   local_badgecerts
 * @copyright 2014 onwards Gregor Anželj, Andraž Prinčič
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */
namespace local_badgecerts\task;

/**
 * Shedule a task.
 */
class delete_files extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('mybadgecertificates', 'local_badgecerts');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG;
        $dir = "{$CFG->dirroot}/local/badgecerts/tmp/";

        foreach (glob($dir . "*.pdf") as $file) {
            if (time() - filectime($file) > 300) {
                unlink($file);
            }
        }
    }
}