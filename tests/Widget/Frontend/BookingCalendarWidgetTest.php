<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Tests\Widget\Frontend;

use Contao\System;
use Contao\TestCase\ContaoTestCase;
use DateTimeImmutable;
use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use Symfony\Component\HttpFoundation\Request;

class BookingCalendarWidgetTest extends ContaoTestCase
{
    protected function setUp(): void
    {
        $container = $this->getContainerWithContaoConfiguration(self::getTempDir());
        $container->get('request_stack')->push(new Request());
        System::setContainer($container);

        $GLOBALS['TL_CONFIG']['dateFormat'] = 'd.m.Y';
        $GLOBALS['TL_LANG']['ERR']['mandatory'] = 'The field %s is mandatory.';
        $GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['invalidDate'] = 'Invalid date.';
        $GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableSubmittedDate'] = 'Unavailable weekday.';
        $GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['unavailableSubmittedFutureDate'] = 'Outside booking period.';
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_CONFIG'], $GLOBALS['TL_LANG']);

        parent::tearDown();
    }

    public function testNormalizesConfigurationAndValues(): void
    {
        $widget = new TestableBookingCalendarWidget([
            'options' => serialize(['1', '7', '7', '9', 'invalid']),
            'bookingCalendarUseMonthYearDropdowns' => '0',
            'bookingCalendarMaxBookingDays' => '14',
            'value' => '2026-08-25',
        ]);

        self::assertSame([1, 7], $widget->getDisabledWeekdays());
        self::assertFalse($widget->isMonthYearDropdownEnabled());
        self::assertSame('2026-08-25', $widget->getIsoValue());
        self::assertSame(
            (new DateTimeImmutable('today +14 days'))->format('Y-m-d'),
            $widget->getMaxBookableDateIso(),
        );
    }

    public function testRejectsInvalidAndUnavailableDates(): void
    {
        $invalidWidget = new TestableBookingCalendarWidget();
        self::assertSame('not-a-date', $invalidWidget->validateInput('not-a-date'));
        self::assertSame(['Invalid date.'], $invalidWidget->getErrors());

        $weekdayWidget = new TestableBookingCalendarWidget([
            'options' => serialize([1]),
        ]);
        self::assertSame('2026-08-24', $weekdayWidget->validateInput('2026-08-24'));
        self::assertSame(['Unavailable weekday.'], $weekdayWidget->getErrors());

        $futureWidget = new TestableBookingCalendarWidget([
            'bookingCalendarMaxBookingDays' => 1,
        ]);
        $futureDate = (new DateTimeImmutable('today +2 days'))->format('Y-m-d');
        self::assertSame($futureDate, $futureWidget->validateInput($futureDate));
        self::assertSame(['Outside booking period.'], $futureWidget->getErrors());
    }

    public function testAcceptsAndNormalizesABookableDate(): void
    {
        $widget = new TestableBookingCalendarWidget();
        $GLOBALS['TL_CONFIG']['dateFormat'] = 'd.m.Y';

        self::assertSame('25.08.2026', $widget->validateInput('2026-08-25'));
        self::assertFalse($widget->hasErrors());
    }
}

class TestableBookingCalendarWidget extends BookingCalendarWidget
{
    public function validateInput(mixed $input): mixed
    {
        return $this->validator($input);
    }
}
