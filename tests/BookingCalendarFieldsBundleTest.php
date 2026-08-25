<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Tests;

use DVC\BookingCalendarFields\BookingCalendarFieldsBundle;
use PHPUnit\Framework\TestCase;

class BookingCalendarFieldsBundleTest extends TestCase
{
    public function testUsesTheModernBundleRoot(): void
    {
        $bundle = new BookingCalendarFieldsBundle();

        self::assertSame(dirname(__DIR__), $bundle->getPath());
        self::assertFileExists($bundle->getPath() . '/config/services.yaml');
        self::assertDirectoryExists($bundle->getPath() . '/contao');
        self::assertDirectoryExists($bundle->getPath() . '/public');
    }
}
