# Moodle Badge certificates (local_badgecerts)
[![Build Status](https://travis-ci.org/atlet/moodle-local_badgecerts.svg?branch=master)](https://travis-ci.org/atlet/moodle-local_badgecerts)

Print certificates based on earned badges in Moodle.

# Features
- Custom certificate design (using svg format).
- Users can download their own certifitates.
- For one badge you can have multiple certificates.
- Teachers can mass print certificates.
- Users can download certifitaces from Moodle Mobile application.

# Screenshots
[Downloaded certificate based on received badge](docs/images/DownloadedBadge.png)

[Vie badge/certificate settings](docs/images/ViewBadgeSettings.png)

[Designing certificate in Inkscape](docs/images/DesigningCertificate.png)

# Documentation
## Requirements
This plugin requires Moodle 3.5+.

## Installation
Install the plugin like any other plugin to folder /local/staticpage.

See [http://docs.moodle.org/en/Installing_plugins](http://docs.moodle.org/en/Installing_plugins) for details on installing Moodle plugins.

## Manage badge certificates
To access menu for editing, adding and managing certificates is found in left menu in every Course - menu entry "Manage badge certificates".

There you can add a new certificate or manage existing.

## Users accessing their certificate
Each user can find their certificates in their profile.

## Designin certificate
[Example certificate](docs/examples/136_potrdilo_varna_raba_interneta.svg)

To design a new certifiate I reccomend [Inkscape](https://inkscape.org/). Text containing placeholders will be changed with value from user profile, badge, ...

### Placeholders
```
[[recipient-fname]] Adds the recipient's first name.
[[recipient-lname]] Adds the recipient's last name.
[[recipient-flname]] Adds the recipient's full name (first, last).
[[recipient-lfname]] Adds the recipient's full name (last, first).
[[recipient-email]] Adds the recipient's email address.
[[issuer-name]] Adds the issuer's name or title.
[[issuer-contact]] Adds the issuer's contact information.
[[badge-name]] Adds the badge's name or title.
[[badge-desc]] Adds the badge's description.
[[badge-number]] Adds the badge's ID number.
[[badge-course]] Adds the name of the course where badge was awarded.
[[badge-hash]] Adds the badge hash value.
[[datetime-Y]] Adds the year.
[[datetime-d.m.Y]] Adds the date in dd.mm.yyyy format.
[[datetime-d/m/Y]] Adds the date in dd/mm/yyyy format.
[[datetime-F]] Adds the date (used in DB datestamps).
[[datetime-s]] Adds Unix Epoch Time timestamp.
[[recipient-birthdate]] Adds the recipient's date of birth.
[[recipient-institution]] Adds the institution where the recipient is employed.
[[badge-date-issued]] Adds the date when badge was issued.

[Booking placeholders](https://github.com/atlet/moodle-mod_booking)
[[booking-name]] Adds the seminar instance name.
[[booking-title]] Adds the seminar title.
[[booking-startdate]] Adds the seminar start date.
[[booking-enddate]] Adds the seminar end date.
[[booking-duration]] Adds the seminar duration.

[Quiz Grading placeholders]()
[[qg-quizname]]',
[[qg-sumgrades]]',
[[qg-firstname]]',
[[qg-up-firstname]]',
[[qg-lastname]]',
[[qg-up-lastname]]',
[[qg-email]]',
[[qg-institution]]',
[[qg-up-institution]]',
[[qg-dosezeno_tock]]',
[[qg-kazenske_tocke]]',
[[qg-moznih_tock]]',
[[qg-procent]]',
[[qg-vprasanja]]',
[[qg-status_kviza]]',
[[qg-datum_resitve]]',
[[qg-datum_vpisa]]',
[[qg-datum_rojstva]]',
[[qg-uvrstitev_posamezniki]]',
[[qg-uvrstitev_skupina]]',
[[qg-organizator]]',
[[qg-lokacija]]',
[[qg-up-organizator]]',
[[qg-up-lokacija]]'
```

# Contributors
Main contributers are Gregor Anželj and Andraž Prinčič.