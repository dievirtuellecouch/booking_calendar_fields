# Booking Calendar Fields

Contao-Bundle für zwei zusätzliche Felder im Formulargenerator:

- **Buchungskalender** mit nicht buchbaren Wochentagen, optionaler Monats-/Jahresauswahl und begrenzbarem Buchungszeitraum
- **Buchungszeit-Auswahl** auf Basis des Contao-Auswahlfeldes

Die Ausgabe verwendet Twig, lädt CSS und JavaScript nur bei tatsächlich verwendeten Feldern und berücksichtigt Pflichtfeld-, Fehler- und Hilfetext-Ausgaben.

## Voraussetzungen

- Contao `^5.7`
- PHP `^8.3`

## Installation

Solange das Paket direkt aus GitHub bezogen wird, wird das Repository einmalig im Contao-Projekt registriert:

```bash
composer config repositories.dvc-booking-calendar-fields vcs https://github.com/dievirtuellecouch/booking_calendar_fields.git
composer require dvc/booking_calendar_fields:^5.7
```

Anschließend muss die Datenbankänderung in der Zielinstallation über den Contao Manager oder kontrolliert über die Contao-Konsole geprüft und ausgeführt werden. Das Bundle ergänzt `tl_form_field` um folgende Spalten:

- `bookingCalendarUseMonthYearDropdowns`
- `bookingCalendarMaxBookingDays`

Die öffentlichen Assets werden bei einer Managed-Edition-Installation durch den üblichen Composer-/Contao-Workflow bereitgestellt.

## Konfiguration

Im Contao-Formulargenerator stehen nach der Installation die Feldtypen „Buchungskalender“ und „Buchungszeit-Auswahl“ zur Verfügung.

Beim Buchungskalender lassen sich nicht buchbare Wochentage, die Monats-/Jahresauswahl und die maximale Anzahl buchbarer Tage ab heute konfigurieren. Ein Wert von `0` oder ein leeres Feld deaktiviert die Begrenzung.

Die Buchungszeit-Auswahl nutzt den vorhandenen Contao-Options-Wizard. Werte und Bezeichnungen werden daher wie bei einem normalen Auswahlfeld gepflegt.

## Entwicklung

```bash
composer install
composer test
```

Zusätzlich sollte das Bundle in einer Contao-5.7-Installation mit `lint:container`, `lint:twig` und einem Formular im Frontend geprüft werden.

## Lizenz

LGPL-3.0-or-later, siehe [LICENSE](LICENSE).
