<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Tests\Contao;

use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use DVC\BookingCalendarFields\Widget\Frontend\BookingTimeSelectWidget;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA'], $GLOBALS['TL_FFL'], $GLOBALS['TL_LANG']);
    }

    public function testRegistersBothFrontendWidgets(): void
    {
        $GLOBALS['TL_FFL'] = [];

        require dirname(__DIR__, 2) . '/contao/config/config.php';

        self::assertSame(BookingCalendarWidget::class, $GLOBALS['TL_FFL'][BookingCalendarWidget::NAME]);
        self::assertSame(BookingTimeSelectWidget::class, $GLOBALS['TL_FFL'][BookingTimeSelectWidget::NAME]);
    }

    public function testDefinesTheContao57PalettesAndDatabaseFields(): void
    {
        $GLOBALS['TL_DCA']['tl_form_field'] = [
            'palettes' => [],
            'fields' => [],
        ];
        $GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarUseMonthYearDropdowns'] = [];
        $GLOBALS['TL_LANG']['tl_form_field']['bookingCalendarMaxBookingDays'] = [];

        require dirname(__DIR__, 2) . '/contao/dca/tl_form_field.php';

        self::assertStringContainsString(
            '{fconfig_legend},mandatory,help',
            $GLOBALS['TL_DCA']['tl_form_field']['palettes'][BookingCalendarWidget::NAME],
        );
        self::assertStringContainsString(
            '{fconfig_legend},mandatory,help',
            $GLOBALS['TL_DCA']['tl_form_field']['palettes'][BookingTimeSelectWidget::NAME],
        );
        self::assertSame(
            ['type' => 'boolean', 'default' => true],
            $GLOBALS['TL_DCA']['tl_form_field']['fields']['bookingCalendarUseMonthYearDropdowns']['sql'],
        );
        self::assertSame(
            ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            $GLOBALS['TL_DCA']['tl_form_field']['fields']['bookingCalendarMaxBookingDays']['sql'],
        );
    }
}
