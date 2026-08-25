<?php

declare(strict_types=1);

use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use DVC\BookingCalendarFields\Widget\Frontend\BookingTimeSelectWidget;

$GLOBALS['TL_FFL'][BookingCalendarWidget::NAME] = BookingCalendarWidget::class;
$GLOBALS['TL_FFL'][BookingTimeSelectWidget::NAME] = BookingTimeSelectWidget::class;
