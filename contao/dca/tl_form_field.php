<?php

declare(strict_types=1);

use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use DVC\BookingCalendarFields\Widget\Frontend\BookingTimeSelectWidget;

$GLOBALS['TL_DCA']['tl_form_field']['palettes'][BookingCalendarWidget::NAME] =
    '{type_legend},type,name,label;'
    . '{fconfig_legend},mandatory,help;'
    . '{booking_calendar_legend},options,bookingCalendarUseMonthYearDropdowns,bookingCalendarMaxBookingDays;'
    . '{expert_legend:hide},class,accesskey;'
    . '{template_legend:hide},customTpl;'
    . '{invisible_legend:hide},invisible';

$GLOBALS['TL_DCA']['tl_form_field']['palettes'][BookingTimeSelectWidget::NAME] =
    '{type_legend},type,name,label;'
    . '{fconfig_legend},mandatory,help;'
    . '{options_legend},options;'
    . '{expert_legend:hide},class,accesskey;'
    . '{template_legend:hide},customTpl;'
    . '{invisible_legend:hide},invisible';

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bookingCalendarUseMonthYearDropdowns'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarUseMonthYearDropdowns'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'default' => true,
    'eval' => [
        'tl_class' => 'w50 clr',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['bookingCalendarMaxBookingDays'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarMaxBookingDays'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
        'rgxp' => 'natural',
        'maxlength' => 10,
        'tl_class' => 'w50',
    ],
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'default' => 0,
    ],
];
