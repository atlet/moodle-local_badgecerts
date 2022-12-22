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
 * Usefull funcitons.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

namespace local_badgecerts;

defined('MOODLE_INTERNAL') || die();

/**
 * Class with variety of usefull funcitons.
 */
class utils {

    /**
     * Check if module booking is installed.
     *
     * @return boolean
     */
    public static function check_mod_booking() {
        global $CFG;

        return file_exists("{$CFG->dirroot}/mod/booking/lib.php");
    }

    /**
     * Check if module quizgrading is installed.
     *
     * @return boolean
     */
    public static function check_mod_quizgrading() {
        global $CFG;

        return file_exists("{$CFG->dirroot}/mod/quizgrading/lib.php");
    }

    /**
     * Returns all booking options.
     *
     * @param  integer $cmid Course module id of Booking.
     * @return array
     */
    public static function get_all_booking_options($cmid) {
        global $DB;

        if (!self::check_mod_booking()) {
            return [];
        }

        $rec = $DB->get_records_sql("
        select
	id,
	text
from
	{booking_options} mbo
where
	bookingid = (
	select
		`instance`
	from
		{course_modules} mcm
	where
		id = :cmid)", ['cmid' => $cmid]);

        $ret = [];
        foreach ($rec as $value) {
            $ret[$value->id] = $value->text;
        }

        return $ret;
    }

    /**
     * Return all Bookings.
     *
     * @return array
     */
    public static function get_all_bookings() {
        global $DB;

        if (!self::check_mod_booking()) {
            return [];
        }

        $rec = $DB->get_records_sql("
        select
        cm.id id,
        concat(c.shortname, ' - ', b.name) name
    from
        {booking} b
    left join {course_modules} cm on
        cm.instance = b.id
        and cm.module = (
        select
            id
        from
            {modules}
        where
            name = 'booking')
    left join {course} c on
        c.id = cm.course");

        $ret = ['' => ''];
        foreach ($rec as $value) {
            $ret[$value->id] = $value->name;
        }

        return $ret;
    }
}
