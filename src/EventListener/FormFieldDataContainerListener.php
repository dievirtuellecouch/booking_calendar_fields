<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Doctrine\DBAL\Connection;
use DVC\BookingCalendarFields\Widget\Frontend\BookingCalendarWidget;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsHook('loadDataContainer')]
class FormFieldDataContainerListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Connection $connection,
    ) {
    }

    public function __invoke(string $table): void
    {
        if ('tl_form_field' !== $table || BookingCalendarWidget::NAME !== $this->getCurrentFormFieldType()) {
            return;
        }

        $fields = &$GLOBALS['TL_DCA']['tl_form_field']['fields'];

        $fields['options']['label'] = $GLOBALS['TL_LANG']['tl_form_field']['booking_calendar_disabled_weekdays'] ?? [
            'Unavailable weekdays',
            'Select unavailable weekdays.',
        ];
        $fields['options']['inputType'] = 'checkbox';
        $fields['options']['options'] = array_keys($GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['weekdays'] ?? []);
        $fields['options']['reference'] = $GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD']['weekdays'] ?? [];
        $fields['options']['eval'] = [
            'multiple' => true,
            'tl_class' => 'clr',
        ];
    }

    private function getCurrentFormFieldType(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return null;
        }

        $postedType = $request->request->get('type');

        if (is_string($postedType) && '' !== $postedType) {
            return $postedType;
        }

        $id = $request->query->get('id');

        if (!is_numeric($id)) {
            return null;
        }

        return $this->connection->fetchOne('SELECT type FROM tl_form_field WHERE id = ?', [(int) $id]) ?: null;
    }
}
