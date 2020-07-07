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
 * Code for upgrading to new versions of the plugin.
 *
 * @package    local_badgecerts
 * @copyright  2014 onwards Gregor Anželj, Andraž Prinčič
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Andraž Prinčič <atletek@gmail.com>, Gregor Anželj <gregor.anzelj@gmail.com>
 */

$string['pluginname'] = 'Badge certificates';

$string['activate'] = 'Enable access';
$string['activatesuccess'] = 'Access to the badges was successfully enabled.';
$string['assign'] = 'Assign';
$string['assignl'] = '< Assign';
$string['assignedbadges'] = 'Badges with assigned badge certificate';
$string['availablebadges'] = 'Badges without assigned badge certificate';
$string['backgroundimage'] = 'Certificate template';
$string['backgroundimage_help'] = 'This is an image that will be used as a certificate template for this badge certificate.

To add a new image, browse and select an image (in SVG format) then click "Save changes".

The SVG file can contain the following (case sensitive) placeholders (in form of text elements):

* **[[badge-name]]** - Adds the badge\'s name or title
* **[[badge-desc]]** - Adds the badge\'s description
* **[[badge-number]]** - Adds the badge\'s ID number
* **[[badge-course]]** - Adds the name of the course where badge was awarded
* **[[badge-hash]]** - Adds the badge hash value
* **[[badge-date-issued]]** - Adds the date when badge was issued
* **[[booking-name]]** - Adds the seminar instance name
* **[[booking-title]]** - Adds the seminar title
* **[[booking-title-n]]** - Add title of option - n is number... it starts wit 1 end finish with 10
* **[[booking-startdate]]** - Adds the seminar start date
* **[[booking-enddate]]** - Adds the seminar end date
* **[[booking-duration]]** - Adds the seminar duration
* **[[datetime-Y]]** - Adds the year
* **[[datetime-d.m.Y]]** - Adds the date in dd.mm.yyyy format
* **[[datetime-d/m/Y]]** - Adds the date in dd/mm/yyyy format
* **[[datetime-F]]** - Adds the date (used in DB datestamps)
* **[[datetime-s]]** - Adds Unix Epoch Time timestamp
* **[[issuer-name]]** - Adds the issuer\'s name or title
* **[[issuer-contact]]** - Adds the issuer\'s contact information
* **[[recipient-birthdate]]** - Adds the recipient\'s date of birth
* **[[recipient-email]]** - Adds the recipient\'s email address
* **[[recipient-institution]]** - Adds the recipient\'s institution
* **[[recipient-fname]]** - Adds the recipient\'s first name
* **[[recipient-flname]]** - Adds the recipient\'s full name (first, last)
* **[[recipient-lfname]]** - Adds the recipient\'s full name (last, first)
* **[[recipient-lname]]** - Adds the recipient\'s last name
* **[[qg-quizname]]** -
* **[[qg-sumgrades]]** -
* **[[qg-firstname]]** -
* **[[qg-up-firstname]]** - upper case first name from quiz grading
* **[[qg-lastname]]** -
* **[[qg-up-lastname]]** -  upper case last name from quiz grading
* **[[qg-email]]** -
* **[[qg-institution]]** -
* **[[qg-up-institution]]** - upper case institution from quiz grading
* **[[qg-dosezeno_tock]]** -
* **[[qg-kazenske_tocke]]** -
* **[[qg-moznih_tock]]** -
* **[[qg-procent]]** -
* **[[qg-vprasanja]]** -
* **[[qg-status_kviza]]** -
* **[[qg-datum_resitve]]** -
* **[[qg-datum_vpisa]]** -
* **[[qg-datum_rojstva]]** - Rojstni datum
* **[[qg-uvrstitev_posamezniki]]** -
* **[[qg-uvrstitev_skupina]]** -
* **[[qg-organizator]]** -
* **[[qg-lokacija]]** -
* **[[qg-up-organizator]]** - Upper case
* **[[qg-up-lokacija]]** - Upper case
';
$string['badgecertificatedetails'] = 'Badge certificate details';
$string['badgecertificateelement'] = 'Badge certificate element';
$string['badgecertificatepreview'] = 'Badge certificate preview';
$string['badgecertificatestatus_0'] = 'Not available to users';
$string['badgecertificatestatus_1'] = 'Available to users';
$string['badgecertificatestatus_2'] = 'Not available to users';
$string['badgecertificatestatus_3'] = 'Available to users';
$string['badgecertificatestatus_4'] = 'Archived';
$string['badgecertsearned'] = 'Number of badge certificates earned: {$a}';
$string['bassign'] = 'Assign to badge';
$string['bdetails'] = 'Edit details';
$string['belements'] = 'Elements';
$string['boverview'] = 'Overview';
$string['bookingid'] = 'Booking ID';
$string['bookingid_help'] = '**Booking ID** is the id (number) of the seminar in the booking module.';
$string['quizgradingid'] = 'Quiz grading ID';
$string['quizgradingid_help'] = '**Quiz grading ID** is the id (number) of the instance ...';
$string['certificatesavailable'] = 'Number of badge certificates available: {$a}';
$string['certificate'] = 'Certificate';
$string['certificateformat'] = 'Format';
$string['certificateformat:A3'] = 'A3 (297x420 mm | 11.69x16.54 in)';
$string['certificateformat:A4'] = 'A4 (210x297 mm | 8.27x11.69 in)';
$string['certificateformat:B4'] = 'B4 (250x353 mm | 9.84x13.90 in)';
$string['certificateformat:B5'] = 'B5 (176x250 mm | 6.93x9.84 in)';
$string['certificateformat:Legal'] = 'Legal (216x356 mm | 8.50x14.00 in)';
$string['certificateformat:Letter'] = 'Letter (216x279 mm | 8.50x11.00 in)';
$string['certificateformat:Tabloid'] = 'Tabloid (279x432 mm | 11.00x17.00 in)';
$string['certificateorientation'] = 'Orientation';
$string['certificateorientation:portrait'] = 'Portrait';
$string['certificateorientation:landscape'] = 'Landscape';
$string['certificateunit'] = 'Unit';
$string['certificateunit:pt'] = 'pt - point';
$string['certificateunit:mm'] = 'mm - millimetre';
$string['certificateunit:cm'] = 'cm - centimetre';
$string['certificateunit:in'] = 'in - inch';
$string['contact'] = 'Contact';
$string['contact_help'] = 'An email address associated with the badge issuer.';
$string['coursebadgecertificates'] = 'Badge certificates';
$string['createcertbutton'] = 'Create badge certificate';
$string['createcertificate'] = 'New badge certificate';
$string['currentimage'] = 'Current template';
$string['datenotdefined'] = 'Date not defined';
$string['deactivate'] = 'Disable access';
$string['deactivatesuccess'] = 'Access to the badges was successfully disabled.';
$string['delbadgecertificate'] = 'Would you like to delete badge certificate \'{$a}\'?';
$string['delconfirmcert'] = 'Delete and remove existing badge certificate';
$string['deletehelpcert'] = '<p>Fully deleting a badge certificate means that all its information will be permanently removed. Users who have used this badge certificate will no longer be able to access it.</p>';
$string['description'] = 'Description';
$string['downloadcertificate'] = 'Download certificate';
$string['error:cannotactcert'] = 'Cannot activate the badge certificate.';
$string['error:clonecert'] = 'Cannot clone the badge certificate.';
$string['error:duplicatecertname'] = 'Badge certificate with such name already exists in the system.';
$string['error:duplicateelement'] = 'Badge certificate element with such content already exists in the system.';
$string['error:nosuchbadgecertelement'] = 'Badge certificate element with id {$a} does not exist.';
$string['error:nosuchbadgecertificate'] = 'Badge certificate with id {$a} does not exist.';
$string['error:savecert'] = 'Cannot save the badge certificate.';
$string['issuerdetails'] = 'Issuer details';
$string['issuername'] = 'Issuer name';
$string['issuername_help'] = 'Name of the issuing agent or authority.';
$string['localbadgecerts'] = 'My badge certificates from {$a} web site';
$string['localbadgecertsh'] = 'My badge certificates from this web site';
$string['localbadgecertsh_help'] = 'All badge certificates earned within this web site by completing courses, course activities, and other requirements.

You can download each badge certificate separately and save them on your computer.';
$string['localbadgecertsp'] = 'Badge certificates from {$a}:';
$string['managebadgecertificates'] = 'Manage badge certificates';
$string['mybadgecertificates'] = 'My badge certificates';
$string['newbadgecertificate'] = 'Add a new badge certificate';
$string['newelement'] = 'Add a new element';
$string['nobadgecertificates'] = 'There are no badge certificates available.';
$string['nobadgecertificateelms'] = 'This badge certificate contains no elements.';
$string['officialtype'] = 'Type';
$string['officialtypedesc'] = 'Choose if this is "official" badge certificate which can be assigned only by admins and managers.';
$string['preview:bookinginstancename'] = 'Booking instance name';
$string['preview:seminartitle'] = 'Seminar title';
$string['preview:seminarduration'] = '8 hours';
$string['preview:recipientfname'] = 'John';
$string['preview:recipientlname'] = 'Doe';
$string['preview:recipientflname'] = 'John Doe';
$string['preview:recipientlfname'] = 'Doe John';
$string['preview:recipientemail'] = 'john.doe@email.com';
$string['preview:recipientinstitution'] = 'Institution';
$string['preview:issuername'] = 'Issuer name';
$string['preview:issuercontact'] = 'info@issuer.org';
$string['preview:badgename'] = 'Badge name';
$string['preview:badgedesc'] = 'This is the description of the badge.';
$string['preview:badgecourse'] = 'Course name';
$string['rawtext'] = 'Content';
$string['remove'] = 'Remove';
$string['remover'] = 'Remove >';
$string['reviewbadgecertificate'] = 'Changes in badge certificate access';
$string['reviewcertconfirm'] = '<p>This will make your badge certificate accessible.</p>

<p>Once a badge certificate has been used it will be <strong>locked</strong> - certain settings including the certificate elements can no longer be changed.</p>

<p>Are you sure you want to enable access to the badge certificate \'{$a}\'?</p>';
$string['sitebadgecertificates'] = 'Site badge certificates';
$string['statuscertmessage_0'] = 'This badge certificate is currently not available to users. Enable access if you want users to be able to use it. ';
$string['statuscertmessage_1'] = 'This badge certificate is currently available to users. Disable access to make any changes. ';
$string['statuscertmessage_2'] = 'This badge certificate is currently not available to users, and its elements are locked. Enable access if you want users to be able to use it. ';
$string['statuscertmessage_3'] = 'This badge certificate is currently available to users, and its elements are locked. ';
$string['titlenotset'] = 'Course title not set';

$string['datetimeformat'] = '%-d. %-m. %Y';

$string['viewcertificates'] = 'Recipients';
$string['filterreport'] = 'Filter report';
$string['reset'] = 'Reset';

$string['fullname'] = 'Recipient';
$string['dateissued'] = 'Date issued';
$string['nctransfers'] = 'Transfered by user';
$string['ndatelasttransfer'] = 'Last time of trasnfer';
$string['nctransfersteacher'] = 'Transfered by you';

$string['printselected'] = 'Print certificate for selected users';
$string['printall'] = 'Print certificate for all users';
$string['nousers'] = 'Can\'t print certificates, because no users found!';

$string['certificatefor'] = 'Certificate for';
$string['certificateforbadge'] = 'Badge';
$string['certificateformodbookingusers'] = 'Booking users';
$string['certificateformodbookinguserssum'] = 'Booking users - sum of options';
$string['certificateformodbookingteachers'] = 'Booking teachers';
$string['certificateforquizgrading'] = 'Quiz grading';
$string['qrcode'] = 'QR code';
$string['qrshow'] = 'Print QR code on certificate?';
$string['qrx'] = 'X position';
$string['qry'] = 'Y position';
$string['qrw'] = 'Width';
$string['qrh'] = 'Height';
$string['qrdata'] = 'Which field to use as QR code data';
$string['userid'] = 'User ID';
$string['username'] = 'User username';

$string['badgecerts:assigncustomcertificate'] = 'Assign custom certificate';
$string['badgecerts:assignofficialcertificate'] = 'Assign official certificate';
$string['badgecerts:certificatemanager'] = 'Certificate manager';
$string['badgecerts:certificatemanagerowninstitution'] = 'Certificate manager - only own institution';
$string['badgecerts:configurecertificate'] = 'Configure certificate';
$string['badgecerts:configureelements'] = 'Configure elements';
$string['badgecerts:createcertificate'] = 'Create certificate';
$string['badgecerts:deletecertificate'] = 'Delete certificate';
$string['badgecerts:printcertificates'] = 'Print certificates';
$string['badgecerts:viewcertificates'] = 'View certificates';
$string['badgecerts:configuredetails'] = 'Configure details';

$string['jeopravil'] = "DA";
$string['niopravil'] = "NI";

$string['datelimit'] = 'Limit conditions';
$string['whenisthisfiltervalid'] = 'Only valid for Booking module!';
$string['usestartandenddate'] = 'Use start and end time';
$string['starttime'] = 'Start time';
$string['endtime'] = 'End time';
$string['certid'] = 'Badge';
$string['reusecertificatetemplate'] = 'Reuse certificate template';
$string['reusetemplate'] = 'Reuse template';
$string['error:selecttemplate'] = 'You must select a file!';
$string['booking'] = 'Booking module';

$string['bookingoptionlimitdatelimit'] = 'Limit for booking options';
$string['enablebookingoptions'] = 'Use booking option limit feature';
$string['bookingoptionsexclude'] = 'Exlude';
$string['bookingoptionsuseonly'] = 'Use only';
$string['bookingoptions'] = 'Booking options';
$string['bookingoptionsinc'] = 'Booking options IDs to include/exclude';
$string['bookingoptions_help'] = 'Use comma (,) to add multiple Booking options.';