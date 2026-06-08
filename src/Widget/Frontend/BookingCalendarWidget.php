<?php

declare(strict_types=1);

namespace DVC\BookingCalendarFields\Widget\Frontend;

use Contao\Date;
use Contao\StringUtil;
use Contao\Widget;
use DateTimeImmutable;

class BookingCalendarWidget extends Widget
{
    public const NAME = 'booking_calendar';

    private const ISO_DATE_FORMAT = 'Y-m-d';
    private const CSS_ASSET = 'bundles/bookingcalendarfields/booking-calendar-field.css|static';
    private const JS_ASSET = 'bundles/bookingcalendarfields/booking-calendar-field.js|static';

    protected $blnSubmitInput = true;
    protected $blnForAttribute = false;
    protected $strTemplate = 'form_booking_calendar';
    protected $strPrefix = 'widget widget-select widget-booking-calendar';

    /** @var list<int> */
    private array $disabledWeekdays = [];
    private bool $monthYearDropdownsEnabled = true;
    private ?int $maxBookingDays = null;

    public function __set($strKey, $varValue): void
    {
        switch ($strKey) {
            case 'options':
                $this->disabledWeekdays = $this->normalizeWeekdays($varValue);
                break;

            case 'bookingCalendarUseMonthYearDropdowns':
                $this->monthYearDropdownsEnabled = $this->normalizeBoolean($varValue, true);
                parent::__set($strKey, $varValue);
                break;

            case 'bookingCalendarMaxBookingDays':
                $this->maxBookingDays = $this->normalizePositiveInteger($varValue);
                parent::__set($strKey, $varValue);
                break;

            case 'mandatory':
                if ($varValue) {
                    $this->arrAttributes['data-required'] = 'true';
                } else {
                    unset($this->arrAttributes['data-required']);
                }

                parent::__set($strKey, $varValue);
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }

    public function parse($arrAttributes = null): string
    {
        $this->addFrontendAssets();
        $this->class = 'booking-calendar-field__widget';

        return parent::parse($arrAttributes);
    }

    public function generate(): string
    {
        return '';
    }

    public function getWrapperClass(): string
    {
        return trim(($this->strPrefix ?? '') . ' ' . ($this->strClass ?? ''));
    }

    public function getLabelClass(): string
    {
        return trim('booking-calendar-field__label ' . $this->getLayoutClass());
    }

    /**
     * @return list<int>
     */
    public function getDisabledWeekdays(): array
    {
        return $this->disabledWeekdays;
    }

    public function getDisabledWeekdaysJson(): string
    {
        return StringUtil::specialchars(json_encode($this->disabledWeekdays, JSON_THROW_ON_ERROR));
    }

    public function getTranslationsJson(): string
    {
        return StringUtil::specialchars(json_encode($this->getTranslations(), JSON_THROW_ON_ERROR));
    }

    public function isMonthYearDropdownEnabled(): bool
    {
        return $this->monthYearDropdownsEnabled;
    }

    public function getMaxBookableDateIso(): string
    {
        return $this->getMaxBookableDate()?->format(self::ISO_DATE_FORMAT) ?? '';
    }

    /**
     * @return array<string, string>
     */
    public function getTranslations(): array
    {
        return [
            'previousMonth' => $this->translate('previousMonth', 'Previous month'),
            'nextMonth' => $this->translate('nextMonth', 'Next month'),
            'monthSelect' => $this->translate('monthSelect', 'Select month'),
            'yearSelect' => $this->translate('yearSelect', 'Select year'),
            'selectDate' => $this->translate('selectDate', 'Select %s'),
            'selectedDate' => $this->translate('selectedDate', 'Selected: %s'),
            'unavailableDate' => $this->translate('unavailableDate', '%s is not bookable'),
        ];
    }

    public function getIsoValue(): string
    {
        if (!is_string($this->varValue) || '' === $this->varValue) {
            return '';
        }

        return $this->parseDate($this->varValue)?->format(self::ISO_DATE_FORMAT) ?? '';
    }

    protected function validator($varInput)
    {
        $varInput = parent::validator($varInput);

        if ($this->hasErrors() || '' === (string) $varInput) {
            return $varInput;
        }

        $date = $this->parseDate((string) $varInput);

        if (!$date instanceof DateTimeImmutable) {
            $this->addError($this->translate('invalidDate', 'Please select a valid date.'));

            return $varInput;
        }

        if (in_array($this->getIsoWeekday($date), $this->disabledWeekdays, true)) {
            $this->addError($this->translate('unavailableSubmittedDate', 'The selected weekday is not bookable.'));

            return $varInput;
        }

        if ($this->isAfterMaxBookableDate($date)) {
            $this->addError($this->translate('unavailableSubmittedFutureDate', 'The selected date is outside the bookable period.'));

            return $varInput;
        }

        return $date->format(Date::getNumericDateFormat());
    }

    private function addFrontendAssets(): void
    {
        $this->addAsset('TL_CSS', self::CSS_ASSET);
        $this->addAsset('TL_JAVASCRIPT', self::JS_ASSET);
    }

    private function addAsset(string $collection, string $asset): void
    {
        if (!isset($GLOBALS[$collection]) || !is_array($GLOBALS[$collection])) {
            $GLOBALS[$collection] = [];
        }

        if (!in_array($asset, $GLOBALS[$collection], true)) {
            $GLOBALS[$collection][] = $asset;
        }
    }

    /**
     * @return list<int>
     */
    private function normalizeWeekdays(mixed $value): array
    {
        $values = StringUtil::deserialize($value, true);
        $weekdays = [];

        foreach ($values as $entry) {
            $weekday = is_array($entry) ? ($entry['value'] ?? null) : $entry;

            if (is_numeric($weekday)) {
                $weekday = (int) $weekday;

                if ($weekday >= 1 && $weekday <= 7) {
                    $weekdays[] = $weekday;
                }
            }
        }

        return array_values(array_unique($weekdays));
    }

    private function normalizeBoolean(mixed $value, bool $default): bool
    {
        if (null === $value) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return 1 === (int) $value;
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
        }

        return $default;
    }

    private function normalizePositiveInteger(mixed $value): ?int
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ('' === $value || !ctype_digit($value)) {
            return null;
        }

        $days = (int) $value;

        return $days > 0 ? $days : null;
    }

    private function getLayoutClass(): string
    {
        $classes = preg_split('/\s+/', $this->strClass ?? '', -1, PREG_SPLIT_NO_EMPTY);
        $classes = array_filter(
            $classes,
            static fn (string $class): bool => 'booking-calendar-field__widget' !== $class
        );

        return implode(' ', $classes);
    }

    private function getMaxBookableDate(): ?DateTimeImmutable
    {
        if (null === $this->maxBookingDays) {
            return null;
        }

        return (new DateTimeImmutable('today'))->modify('+' . $this->maxBookingDays . ' days');
    }

    private function isAfterMaxBookableDate(DateTimeImmutable $date): bool
    {
        $maxBookableDate = $this->getMaxBookableDate();

        if (!$maxBookableDate instanceof DateTimeImmutable) {
            return false;
        }

        return $date > $maxBookableDate;
    }

    private function parseDate(string $value): ?DateTimeImmutable
    {
        foreach ([self::ISO_DATE_FORMAT, Date::getNumericDateFormat()] as $format) {
            $date = DateTimeImmutable::createFromFormat('!' . $format, $value);
            $errors = DateTimeImmutable::getLastErrors();

            if ($date instanceof DateTimeImmutable && (false === $errors || (0 === $errors['warning_count'] && 0 === $errors['error_count']))) {
                return $date;
            }
        }

        return null;
    }

    private function getIsoWeekday(DateTimeImmutable $date): int
    {
        return (int) $date->format('N');
    }

    private function translate(string $key, string $fallback): string
    {
        $value = $GLOBALS['TL_LANG']['DVC_BOOKING_CALENDAR_FIELD'][$key] ?? null;

        return is_string($value) && '' !== $value ? $value : $fallback;
    }
}
