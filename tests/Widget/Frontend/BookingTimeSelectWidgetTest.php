<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Tests\Widget\Frontend;

use Contao\System;
use Contao\TestCase\ContaoTestCase;
use DVC\BookingCalendarFields\Widget\Frontend\BookingTimeSelectWidget;
use Symfony\Component\HttpFoundation\Request;

class BookingTimeSelectWidgetTest extends ContaoTestCase
{
    protected function setUp(): void
    {
        $container = $this->getContainerWithContaoConfiguration(self::getTempDir());
        $container->get('request_stack')->push(new Request());
        System::setContainer($container);
    }

    public function testProvidesStableFrontendClasses(): void
    {
        $widget = new BookingTimeSelectWidget([
            'class' => 'layout-class',
        ]);

        self::assertSame(
            'widget widget-select widget-booking-time-select booking-time-select layout-class',
            $widget->getWrapperClass(),
        );
        self::assertSame('layout-class booking-time-select__control', $widget->getControlClass());
        self::assertSame('booking-time-select__label layout-class', $widget->getLabelClass());
    }
}
