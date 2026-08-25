<?php

declare(strict_types=1);

use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use DVC\BookingCalendarFields\Widget\Frontend\BookingTimeSelectWidget;

$GLOBALS['TL_LANG']['FFL'][BookingCalendarWidget::NAME] = [
    'Booking calendar',
    'Shows an accessible monthly calendar for selecting a bookable date.',
];

$GLOBALS['TL_LANG']['FFL'][BookingTimeSelectWidget::NAME] = [
    'Booking time select',
    'Shows a select field with configurable booking times.',
];

$GLOBALS['TL_LANG']['tl_form_field']['booking_calendar_legend'] = 'Booking calendar';
$GLOBALS['TL_LANG']['tl_form_field']['booking_calendar_disabled_weekdays'] = [
    'Unavailable weekdays',
    'Select the weekdays that should not be bookable in the calendar.',
];
$GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarUseMonthYearDropdowns'] = [
    'Enable month and year selection',
    'Shows the dropdown selection for month and year above the calendar.',
];
$GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarMaxBookingDays'] = [
    'Bookable up to days',
    'Specify how many days from today should be bookable. 0 or empty means no limit.',
];

$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['weekdays'] = [
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thursday',
    '5' => 'Friday',
    '6' => 'Saturday',
    '7' => 'Sunday',
];

$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['previousMonth'] = 'Previous month';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['nextMonth'] = 'Next month';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['monthSelect'] = 'Select month';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['yearSelect'] = 'Select year';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['selectDate'] = 'Select %s';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['selectedDate'] = 'Selected: %s';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableDate'] = '%s is not bookable';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['invalidDate'] = 'Please select a valid date.';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableSubmittedDate'] = 'The selected weekday is not bookable.';
$GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableSubmittedFutureDate'] = 'The selected date is outside the bookable period.';
