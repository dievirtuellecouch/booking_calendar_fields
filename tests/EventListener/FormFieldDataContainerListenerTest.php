<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Tests\EventListener;

use Doctrine\DBAL\Connection;
use DVC\BookingCalendarFields\EventListener\FormFieldDataContainerListener;
use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FormFieldDataContainerListenerTest extends TestCase
{
    private Connection&MockObject $connection;
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $GLOBALS['TL_DCA']['tl_form_field']['fields']['options'] = [
            'inputType' => 'optionWizard',
            'sql' => 'blob NULL',
        ];
        $GLOBALS['TL_LANG']['tl_form_field']['booking_calendar_disabled_weekdays'] = [
            'Unavailable weekdays',
            'Select unavailable weekdays.',
        ];
        $GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['weekdays'] = [
            1 => 'Monday',
            7 => 'Sunday',
        ];

        $this->connection = $this->createMock(Connection::class);
        $this->requestStack = new RequestStack();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA'], $GLOBALS['TL_LANG']);
    }

    public function testIgnoresOtherTables(): void
    {
        $originalFields = $GLOBALS['TL_DCA']['tl_form_field']['fields'];
        $this->connection->expects(self::never())->method('fetchOne');

        $this->createListener()('tl_form');

        self::assertSame($originalFields, $GLOBALS['TL_DCA']['tl_form_field']['fields']);
    }

    public function testUsesThePostedFieldTypeWithoutQueryingTheDatabase(): void
    {
        $this->requestStack->push(new Request([], ['type' => BookingCalendarWidget::NAME]));
        $this->connection->expects(self::never())->method('fetchOne');

        $this->createListener()('tl_form_field');

        $options = $GLOBALS['TL_DCA']['tl_form_field']['fields']['options'];

        self::assertSame('checkbox', $options['inputType']);
        self::assertSame([1, 7], $options['options']);
        self::assertSame([1 => 'Monday', 7 => 'Sunday'], $options['reference']);
        self::assertSame('blob NULL', $options['sql']);
    }

    public function testLoadsTheStoredFieldTypeUsingAParameterizedQuery(): void
    {
        $this->requestStack->push(new Request(['id' => '42']));
        $this->connection
            ->expects(self::once())
            ->method('fetchOne')
            ->with('SELECT type FROM tl_form_field WHERE id = ?', [42])
            ->willReturn(BookingCalendarWidget::NAME)
        ;

        $this->createListener()('tl_form_field');

        self::assertSame(
            'checkbox',
            $GLOBALS['TL_DCA']['tl_form_field']['fields']['options']['inputType'],
        );
    }

    private function createListener(): FormFieldDataContainerListener
    {
        return new FormFieldDataContainerListener($this->requestStack, $this->connection);
    }
}
