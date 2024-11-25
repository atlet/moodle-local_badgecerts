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

$string['activate'] = 'Zugriff erlauben';
$string['activatesuccess'] = 'Der Zugriff auf die Badges wurde erlaubt.';
$string['assign'] = 'Zuordnen';
$string['assignl'] = '< Zuordnen';
$string['assignedbadges'] = 'Badges mit zugeordneten Zertifikaten';
$string['availablebadges'] = 'Badges ohne zugeordneten Zertifikaten';
$string['backgroundimage'] = 'Zertifikatsvorlage';
$string['backgroundimage_help'] = 'Hierbei handelt es sich um ein Bild, welches als Zertifikats-Vorlage für diesen Badge genutzt wird.

Um ein neues Bild hinzuzufügen, wählen sie eine Datei (SVG format) und klicken Sie "Änderungen speichern".

Die SVG Datei kann Textelemente für folgende Platzhalter (Gross- / Kleinschreibung beachten!) beinhalten

* **[[badge-name]]** - Fügt den Namen oder Titel des Badges ein
* **[[badge-desc]]** - Fügt die Beschreibung des Badges ein
* **[[badge-number]]** - Fügt die Badge-ID ein
* **[[badge-course]]** - Fügt den Namens des Kurses ein, in welchem der Badge erteilt wurde
* **[[badge-hash]]** - Fügt den Hash des Badges hinzu
* **[[badge-date-issued]]** - Fügt das Austellungsdatum des Badges ein
* **[[booking-name]]** - Fügt den Namen der Seminar-Instanz hinzu
* **[[booking-title]]** - Fügt den Titel des Seminars hinzu
* **[[booking-title-n]]** - Fügt den Titel der Option ein. n ist dabei die Nummer. n startet bei 1 und endet mit 10
* **[[booking-startdate]]** - Fügt das Startdatum des Seminars ein
* **[[booking-enddate]]** - Fügt das Enddatum des Seminars ein
* **[[booking-duration]]** - Fügt die Dauer des Seminars ein
* **[[datetime-Y]]** - Fügt das Jahr ein
* **[[datetime-d.m.Y]]** - Fügt das Datum im Format dd.mm.yyyy ein
* **[[datetime-d/m/Y]]** - Fügt das Datum im Format dd/mm/yyyy ein
* **[[datetime-F]]** - Fügt das Datum als DB datestamps ein
* **[[datetime-s]]** - Fügt Unix Epoch Time timestamp ein
* **[[issuer-name]]** - Fügt den Namen des Ausstellers oder den Titel ein
* **[[issuer-contact]]** - Fügt die Kontaktangaben des Ausstellers ein
* **[[recipient-birthdate]]** - Fügt das Geburtsdatum des Empfänger ein
* **[[recipient-email]]** - Fügt die E-Mail-Adresse des Empfängers ein
* **[[recipient-institution]]** - Fügt die Institution des Empfänger ein
* **[[recipient-fname]]** - Fügt den Vornamen des Empfängers ein
* **[[recipient-flname]]** - Fügt den vollen Namen des Empfängers ein (Vorname, Nachname)
* **[[recipient-lfname]]** - Fügt den vollen Namen des Empfängers ein (Nachname, Vorname)
* **[[recipient-lname]]** - Fügt den Nachnamen des Empfängers ein
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
$string['badgecertificatedetails'] = 'Vorlage Badge-Zertifikat';
$string['badgecertificateelement'] = 'Element Badge-Zertifikat';
$string['badgecertificatepreview'] = 'Vorschau Badge-Zertifikat';
$string['badgecertificatestatus_0'] = 'Für Nutzer nicht verfügbar';
$string['badgecertificatestatus_1'] = 'Für Nutzer verfügbar';
$string['badgecertificatestatus_2'] = 'Für Nutzer nicht verfügbar';
$string['badgecertificatestatus_3'] = 'Für Nutzer verfügbar';
$string['badgecertificatestatus_4'] = 'Archiv';
$string['badgecertsearned'] = 'Anzahl erhaltene Badge-Zertifikate: {$a}';
$string['bassign'] = 'Zu Badge hinzufügen';
$string['bdetails'] = 'Details bearbeiten';
$string['belements'] = 'Elemente';
$string['boverview'] = 'Übersicht';
$string['bookingid'] = 'Buchungs ID';
$string['bookingid_help'] = '**Buchungs ID** ist die id (Nummer) des Seminars im booking Module.';
$string['quizgradingid'] = 'Quiz Beurteilungs ID';
$string['quizgradingid_help'] = '**Quiz Beurteilungs ID** ist die id (Nummer) von der Instanz ...';
$string['certificatesavailable'] = 'Anzahl verfügbarer Badge-Zertifikate: {$a}';
$string['certificate'] = 'Zertifikat';
$string['certificateformat'] = 'Format';
$string['certificateformat:A3'] = 'A3 (297x420 mm | 11.69x16.54 in)';
$string['certificateformat:A4'] = 'A4 (210x297 mm | 8.27x11.69 in)';
$string['certificateformat:B4'] = 'B4 (250x353 mm | 9.84x13.90 in)';
$string['certificateformat:B5'] = 'B5 (176x250 mm | 6.93x9.84 in)';
$string['certificateformat:Legal'] = 'Legal (216x356 mm | 8.50x14.00 in)';
$string['certificateformat:Letter'] = 'Letter (216x279 mm | 8.50x11.00 in)';
$string['certificateformat:Tabloid'] = 'Tabloid (279x432 mm | 11.00x17.00 in)';
$string['certificateorientation'] = 'Ausrichtung';
$string['certificateorientation:portrait'] = 'Portrait (Hochformat)';
$string['certificateorientation:landscape'] = 'Landscape (Querformat)';
$string['certificateunit'] = 'Einheit';
$string['certificateunit:pt'] = 'pt - Point';
$string['certificateunit:mm'] = 'mm - Millimeter';
$string['certificateunit:cm'] = 'cm - Zentimeter';
$string['certificateunit:in'] = 'in - Inch';
$string['contact'] = 'Kontakt';
$string['contact_help'] = 'Ein E-Mail-Adresse, welche mit dem Badge-Aussteller in Verbindung steht.';
$string['coursebadgecertificates'] = 'Badge Zertifikate';
$string['createcertbutton'] = 'Erstelle Badge Zertifikate';
$string['createcertificate'] = 'Neues Badge Zertifikat';
$string['currentimage'] = 'Aktuelle Vorlage';
$string['datenotdefined'] = 'Datum nicht definiert';
$string['deactivate'] = 'Zugriff verweigern';
$string['deactivatesuccess'] = 'Der Zugriff auf die Badges wurde verweigert.';
$string['delbadgecertificate'] = 'Möchten Sie das Badge-Zertifikat \'{$a}\' wirklich löschen?';
$string['delconfirmcert'] = 'Löschen des bestehenden Badge-Zertifikates';
$string['deletehelpcert'] = '<p>Das vollständige Löschen eines Badge-Zertifikats bedeutet, dass alle damit verbunden Informationen permanent gelöscht werden. Nutzer, welche dieses Badge-Zertifikat benutzt haben, werden nicht mehr in der Lage sein, dieses zu nutzen.</p>';
$string['description'] = 'Beschreibung';
$string['downloadcertificate'] = 'Zertifikat herunterladen';
$string['error:cannotactcert'] = 'Kann das Badge-Zertifikat nicht aktivieren.';
$string['error:clonecert'] = 'Kann das Badge-Zertifikat nicht verdopplen';
$string['error:duplicatecertname'] = 'Es exitiert bereits ein Badge-Zertifikat mit diesem Namen im System.';
$string['error:duplicateelement'] = 'Ein Badge-Zertifikat-Element mit diesem Inhalt exisitert bereits im System.';
$string['error:nosuchbadgecertelement'] = 'Das Badge-Zertifikat-Element mit der id {$a} exisitert nicht.';
$string['error:nosuchbadgecertificate'] = 'Das Badge-Zertifikat mit der id {$a} existiert nicht.';
$string['error:savecert'] = 'Kann das Badge-Zertifikat nicht speichern.';
$string['issuerdetails'] = 'Details des Ausstellers';
$string['issuername'] = 'Name des Ausstellers';
$string['issuername_help'] = 'Name des ausstellenden Agenten oder der austellenden Authorität.';
$string['localbadgecerts'] = 'Meine Badge-Zertifkate von der Webseite: {$a}';
$string['localbadgecertsh'] = 'Meine Badge-Zertifkate von dieser Webseite.';
$string['localbadgecertsh_help'] = 'All badge certificates earned within this web site by completing courses, course activities, and other requirements.

You can download each badge certificate separately and save them on your computer.';
$string['localbadgecertsp'] = 'Badge certificates from {$a}:';
$string['managebadgecertificates'] = 'Meine Badge-Zertifikate';
$string['mybadgecertificates'] = 'Meine Badge-Zertifikate';
$string['newbadgecertificate'] = 'Ein Badge-Zertifikat hinzufügen';
$string['newelement'] = 'Neues Element hinzufügen';
$string['nobadgecertificates'] = 'Es stehen keine Badge-Zertifikate zur Verfügung.';
$string['nobadgecertificateelms'] = 'Dieses Badge-Zertifikat enthält keine Elemente.';
$string['officialtype'] = 'Typ';
$string['officialtypedesc'] = 'Anwählen, wenn es sich bei diesem Zertfikat um ein "offizielles" Zertifikat handelt, welches nur durch Administratoren und Manager ausgestellt werden kann.';
$string['preview:bookinginstancename'] = 'Buchungs-Instanz-Name';
$string['preview:seminartitle'] = 'Seminar Titel';
$string['preview:seminarduration'] = '8 Stunden';
$string['preview:recipientfname'] = 'John';
$string['preview:recipientlname'] = 'Doe';
$string['preview:recipientflname'] = 'John Doe';
$string['preview:recipientlfname'] = 'Doe John';
$string['preview:recipientemail'] = 'john.doe@email.com';
$string['preview:recipientinstitution'] = 'Institution';
$string['preview:issuername'] = 'Name des Ausstellers';
$string['preview:issuercontact'] = 'info@issuer.org';
$string['preview:badgename'] = 'Badge Name';
$string['preview:badgedesc'] = 'Dies ist die Beschreibung des Badges.';
$string['preview:badgecourse'] = 'Kursname';
$string['rawtext'] = 'Inhalt';
$string['remove'] = 'Entfernen';
$string['remover'] = 'Entfernen >';
$string['reviewbadgecertificate'] = 'Änderungen von Zugriffen des Badge-Zertifiakts.';
$string['reviewcertconfirm'] = '<p>Dies macht ihr Zertifikat verfügbar.</p>

<p>Sobald ein Badge-Zertifikat benutzt wurde wird es <strong>gesperrt</strong> - gewisse Einstellungen inklusive der Zertifikats-Elemente können nicht mehr verändert werden.</p>

<p>Sind Sie sicher, dass Sie den das Zertifikat \'{$a}\'verfügbar machen wollen?</p>';
$string['sitebadgecertificates'] = 'Site-Badge-Zertifikate';
$string['statuscertmessage_0'] = 'Dieses Badge-Zertifikat ist im Moment für Nutzer nicht verfügbar. Aktivieren Sie den Zugrff, wenn Sie wollen, dass Nutzer dieses nutzen können.';
$string['statuscertmessage_1'] = 'Dieses Badge-Zertifikat wird im Moment von Nutzern verwendet. Verweigern Sie den Zugriff, um Änderungen zu machen. ';
$string['statuscertmessage_2'] = 'This badge certificate is currently not available to users, and its elements are locked. Enable access if you want users to be able to use it. ';
$string['statuscertmessage_3'] = 'This badge certificate is currently available to users, and its elements are locked. ';
$string['titlenotset'] = 'Kurstitel ist nicht gesetzt';

$string['datetimeformat'] = '%-d. %-m. %Y';

$string['viewcertificates'] = 'Empfänger';
$string['filterreport'] = 'Filter Report';
$string['reset'] = 'Reset';

$string['fullname'] = 'Empfänger';
$string['dateissued'] = 'Ausstelungsdatum';
$string['nctransfers'] = 'Von Nutzern heruntergeladen';
$string['ndatelasttransfer'] = 'Letzter Download';
$string['nctransfersteacher'] = 'Durch Sie heruntergeladen';

$string['printselected'] = 'Zertifikat für ausgewählte Nutzer drucken';
$string['printall'] = 'Zertifikat für alle Nutzer drucken';
$string['nousers'] = 'Kann das Zertifikat nicht drucken, da keine Nutzer gefunden wurden.';

$string['certificatefor'] = 'Zertifikat für';
$string['certificateforbadge'] = 'Badge';
$string['certificateformodbookingusers'] = 'Booking Nutzer';
$string['certificateformodbookinguserssum'] = 'Booking Nutzer - Summe der Optionen';
$string['certificateformodbookingteachers'] = 'Booking Lehrpersonen';
$string['certificateforquizgrading'] = 'Quiz Beurteilung';
$string['qrcode'] = 'QR-Code';
$string['qrshow'] = 'QR auf dem Zertfikat drucken?';
$string['qrx'] = 'X Position';
$string['qry'] = 'Y Position';
$string['qrw'] = 'Breite';
$string['qrh'] = 'Höhe';
$string['qrdata'] = 'Welches Feld soll für die QR-Code-Daten verwendent werden';
$string['userid'] = 'User ID';
$string['username'] = 'User username';

$string['badgecerts:assigncustomcertificate'] = 'Eigenes Zertifikat zuordnen';
$string['badgecerts:assignofficialcertificate'] = 'Offizielles Zertifikat zuordnen';
$string['badgecerts:certificatemanager'] = 'Zertifikats-Manager';
$string['badgecerts:certificatemanagerowninstitution'] = 'Zertifikats-Manager - nur eigenen Origanisation';
$string['badgecerts:configurecertificate'] = 'Zertifikat konfigurieren';
$string['badgecerts:configureelements'] = 'Elemete konfigurieren';
$string['badgecerts:createcertificate'] = 'Zertifikat erstellen';
$string['badgecerts:deletecertificate'] = 'Zertifikat löschen';
$string['badgecerts:printcertificates'] = 'Zertifikat drucken';
$string['badgecerts:viewcertificates'] = 'Zertifikate anzeigen';
$string['badgecerts:configuredetails'] = 'Details konfigurieren';

$string['jeopravil'] = "DA";
$string['niopravil'] = "NI";

$string['datelimit'] = 'Limitierende Bedingungen';
$string['whenisthisfiltervalid'] = 'Nur gültig für eine gültige Buchung!';
$string['usestartandenddate'] = 'Benutzen Sie die Start- und End-Zeit';
$string['starttime'] = 'Startzeit';
$string['endtime'] = 'Endzeit';
$string['certid'] = 'Badge';
$string['reusecertificatetemplate'] = 'Zertifikats-Vorlage wiederverwenden';
$string['reusetemplate'] = 'Vorlage wiederverwenden';
$string['error:selecttemplate'] = 'Sie müssen einen Datei wählen!';
$string['booking'] = 'Booking module';

$string['bookingoptionlimitdatelimit'] = 'Limit der Buchungs-Optionen';
$string['enablebookingoptions'] = 'Nutzen Sie die Möglichkeit der Limitierung von Buchungs-Optionen';
$string['bookingoptionsexclude'] = 'Ausschliessen';
$string['bookingoptionsuseonly'] = 'Benutzen Sie nur';
$string['bookingoptions'] = 'Buchungs-Optionen';
$string['bookingoptionsinc'] = 'Buchungs-Optionen-IDs einzebeziehen / ausschliessen';
$string['bookingoptions_help'] = 'Benutzen Sie das Komma (,), um mehrere Buchungs-Optionen zu nutzen.';
