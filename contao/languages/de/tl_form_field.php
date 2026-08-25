<?php

declare(strict_types=1);

use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use DVC\BookingCalendarFields\Widget\Frontend\BookingTimeSelectWidget;

$GLOBALS['TL_LANG']['FFL'][BookingCalendarWidget::NAME] = [
    'Buchungskalender',
    'Zeigt einen barrierearmen Monatskalender zur Auswahl eines buchbaren Datums an.',
];

$GLOBALS['TL_LANG']['FFL'][BookingTimeSelectWidget::NAME] = [
    'Buchungszeit-Auswahl',
    'Zeigt ein Auswahlfeld mit konfigurierbaren Uhrzeiten zur Buchung an.',
];

$GLOBALS['TL_LANG']['tl_form_field']['booking_calendar_legend'] = 'Buchungskalender';
$GLOBALS['TL_LANG']['tl_form_field']['booking_calendar_disabled_weekdays'] = [
    'Nicht buchbare Wochentage',
    'Wählen Sie die Wochentage aus, die im Kalender nicht buchbar sein sollen.',
];
$GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarUseMonthYearDropdowns'] = [
    'Monats- und Jahresauswahl aktivieren',
    'Blendet die Dropdown-Auswahl für Monat und Jahr über dem Kalender ein.',
];
$GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarMaxBookingDays'] = [
    'Buchbar bis in Tage',
    'Geben Sie an, wie viele Tage ab heute buchbar sein sollen. 0 oder leer bedeutet ohne Begrenzung.',
];

$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['weekdays'] = [
    '1' => 'Montag',
    '2' => 'Dienstag',
    '3' => 'Mittwoch',
    '4' => 'Donnerstag',
    '5' => 'Freitag',
    '6' => 'Samstag',
    '7' => 'Sonntag',
];

$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['previousMonth'] = 'Vorheriger Monat';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['nextMonth'] = 'Nächster Monat';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['monthSelect'] = 'Monat auswählen';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['yearSelect'] = 'Jahr auswählen';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['selectDate'] = '%s auswählen';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['selectedDate'] = 'Ausgewählt: %s';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableDate'] = '%s ist nicht buchbar';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['invalidDate'] = 'Bitte wählen Sie ein gültiges Datum.';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableSubmittedDate'] = 'Der gewählte Wochentag ist nicht buchbar.';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableSubmittedFutureDate'] = 'Das gewählte Datum liegt außerhalb des buchbaren Zeitraums.';
