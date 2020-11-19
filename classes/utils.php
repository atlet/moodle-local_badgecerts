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
 * @package    local_badgecerts
 * @copyright  2014 onwards Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>
 */

namespace local\badgecerts\classes;

defined('MOODLE_INTERNAL') || die();

class utils {

    /**
     * Check if module booking is installed.
     *
     * @return boolean
     */
    public function check_mod_booking() {
        global $CFG;

        return file_exists("{$CFG->dirroot}/mod/booking/lib.php");
    }

    /**
     * Check if module quizgrading is installed.
     *
     * @return boolean
     */
    public function check_mod_quizgrading() {
        global $CFG;

        return file_exists("{$CFG->dirroot}/mod/quizgrading/lib.php");
    }

}