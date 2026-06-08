<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Widget\Frontend;

use Contao\FormSelect;

class BookingTimeSelectWidget extends FormSelect
{
    public const NAME = 'booking_time_select';

    private const CSS_ASSET = 'bundles/bookingcalendarfields/booking-calendar-field.css|static';

    protected $strTemplate = 'form_booking_time_select';
    protected $strPrefix = 'widget widget-select widget-booking-time-select';

    public function parse($arrAttributes = null): string
    {
        $this->addFrontendCss();

        return parent::parse($arrAttributes);
    }

    public function getWrapperClass(): string
    {
        return trim($this->strPrefix . ' booking-time-select ' . $this->getLayoutClass());
    }

    public function getControlClass(): string
    {
        return trim($this->strClass . ' booking-time-select__control');
    }

    public function getLabelClass(): string
    {
        return trim('booking-time-select__label ' . $this->getLayoutClass());
    }

    private function addFrontendCss(): void
    {
        if (!isset($GLOBALS['TL_CSS']) || !is_array($GLOBALS['TL_CSS'])) {
            $GLOBALS['TL_CSS'] = [];
        }

        if (!in_array(self::CSS_ASSET, $GLOBALS['TL_CSS'], true)) {
            $GLOBALS['TL_CSS'][] = self::CSS_ASSET;
        }
    }

    private function getLayoutClass(): string
    {
        $classes = preg_split('/\s+/', $this->strClass ?? '', -1, PREG_SPLIT_NO_EMPTY);
        $classes = array_filter(
            $classes,
            static fn (string $class): bool => !in_array($class, ['select', 'multiselect', 'tl_chosen'], true)
        );

        return implode(' ', $classes);
    }
}
