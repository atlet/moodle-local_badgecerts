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
 * Page for badge certificates management.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/local/badgecerts/lib.php');

$hash       = required_param('hash', PARAM_ALPHA);
$certid = required_param('certid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$filename = required_param('filename', PARAM_ALPHA);

$badges = array();

$user = (object) array();

$user->userid = $userid;
$user->hash = $hash;

$badges[$userid] = $user;

$pdfdata = bulk_generate_certificates($certid, $badges, 'S');

$dir = sys_get_temp_dir();

file_put_contents("{$dir}/{$filename}", $pdfdata);

$file = "{$dir}/{$filename}";

header('Content-type: application/octet-stream');
header("Content-Type: ".mime_content_type($file));
header("Content-Disposition: attachment; filename=".$filename);

readfile($file);