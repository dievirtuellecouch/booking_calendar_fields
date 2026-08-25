(function () {
  'use strict';

  var calendarSelector = '[data-booking-calendar-field]';
  var locale = document.documentElement.lang || navigator.language || 'de-DE';
  var isoFormat = /^\d{4}-\d{2}-\d{2}$/;

  function parseJson(value, fallback) {
    try {
      return JSON.parse(value || '');
    } catch (error) {
      return fallback;
    }
  }

  function pad(value) {
    return String(value).padStart(2, '0');
  }

  function toIsoDate(date) {
    return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
  }

  function parseIsoDate(value) {
    if (!isoFormat.test(value || '')) {
      return null;
    }

    var parts = value.split('-').map(Number);
    var date = new Date(parts[0], parts[1] - 1, parts[2]);

    if (date.getFullYear() !== parts[0] || date.getMonth() !== parts[1] - 1 || date.getDate() !== parts[2]) {
      return null;
    }

    return date;
  }

  function getIsoWeekday(date) {
    return date.getDay() === 0 ? 7 : date.getDay();
  }

  function formatDate(date) {
    return new Intl.DateTimeFormat(locale, {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    }).format(date);
  }

  function formatMonth(date) {
    return new Intl.DateTimeFormat(locale, {
      month: 'long'
    }).format(date);
  }

  function text(template, value) {
    return String(template || '%s').replace('%s', value);
  }

  function clearElement(element) {
    while (element.firstChild) {
      element.removeChild(element.firstChild);
    }
  }

  function createOption(value, label, selected) {
    var option = document.createElement('option');
    option.className = 'booking-calendar-field__current-option';
    option.value = String(value);
    option.textContent = label;
    option.selected = selected;

    return option;
  }

  function showSelect(select, button) {
    select.hidden = false;
    button.hidden = true;
    select.classList.add('booking-calendar-field__current-select--open');
    button.classList.add('booking-calendar-field__current-button--hidden');
    select.focus();

    if (typeof select.showPicker === 'function') {
      try {
        select.showPicker();
      } catch (error) {
        // Some browsers only allow showPicker() during direct user activation.
      }
    }
  }

  function hideSelect(select, button) {
    select.hidden = true;
    button.hidden = false;
    select.classList.remove('booking-calendar-field__current-select--open');
    button.classList.remove('booking-calendar-field__current-button--hidden');
  }

  function setButtonExpanded(button, expanded) {
    if (button && button.hasAttribute('aria-expanded')) {
      button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }
  }

  function initializeCalendar(root) {
    var input = root.querySelector('[data-booking-calendar-input]');
    var grid = root.querySelector('[data-calendar-grid]');
    var weekdaysRow = root.querySelector('[data-calendar-weekdays]');
    var status = root.querySelector('[data-calendar-status]');
    var previousButton = root.querySelector('[data-calendar-prev]');
    var nextButton = root.querySelector('[data-calendar-next]');
    var monthButton = root.querySelector('[data-calendar-month-button]');
    var yearButton = root.querySelector('[data-calendar-year-button]');
    var monthSelect = root.querySelector('[data-calendar-month-select]');
    var yearSelect = root.querySelector('[data-calendar-year-select]');
    var dropdownsEnabled = root.dataset.monthYearDropdowns !== 'false';
    var maxBookableDate = parseIsoDate(root.dataset.maxBookableDate);

    if (!input || !grid || !weekdaysRow || !previousButton || !nextButton || !monthButton || !yearButton) {
      return;
    }

    if (dropdownsEnabled && (!monthSelect || !yearSelect)) {
      return;
    }

    var disabledWeekdays = parseJson(root.dataset.disabledWeekdays, []).map(Number);
    var translations = parseJson(root.dataset.translations, {});
    var selectedDate = parseIsoDate(input.value);
    var today = new Date();
    var visibleDate = selectedDate ? new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1) : new Date(today.getFullYear(), today.getMonth(), 1);

    root.classList.add('booking-calendar-field--initialized');
    root.classList.toggle('booking-calendar-field--dropdowns-enabled', dropdownsEnabled);
    root.classList.toggle('booking-calendar-field--dropdowns-disabled', !dropdownsEnabled);

    function isOutsideBookingWindow(date) {
      return maxBookableDate instanceof Date && date > maxBookableDate;
    }

    function renderWeekdays() {
      clearElement(weekdaysRow);

      for (var weekday = 1; weekday <= 7; weekday += 1) {
        var base = new Date(2024, 0, weekday);
        var th = document.createElement('th');
        th.className = 'booking-calendar-field__weekday booking-calendar-field__weekday--' + weekday;
        th.dataset.weekday = String(weekday);
        th.scope = 'col';
        th.textContent = new Intl.DateTimeFormat(locale, { weekday: 'short' }).format(base);
        weekdaysRow.appendChild(th);
      }
    }

    function renderControls() {
      monthButton.textContent = formatMonth(visibleDate);
      yearButton.textContent = String(visibleDate.getFullYear());

      if (!dropdownsEnabled) {
        return;
      }

      clearElement(monthSelect);
      clearElement(yearSelect);

      for (var month = 0; month < 12; month += 1) {
        monthSelect.appendChild(createOption(month, formatMonth(new Date(visibleDate.getFullYear(), month, 1)), month === visibleDate.getMonth()));
      }

      for (var year = visibleDate.getFullYear() - 5; year <= visibleDate.getFullYear() + 10; year += 1) {
        yearSelect.appendChild(createOption(year, String(year), year === visibleDate.getFullYear()));
      }
    }

    function renderGrid() {
      clearElement(grid);

      var firstDay = new Date(visibleDate.getFullYear(), visibleDate.getMonth(), 1);
      var daysInMonth = new Date(visibleDate.getFullYear(), visibleDate.getMonth() + 1, 0).getDate();
      var startOffset = getIsoWeekday(firstDay) - 1;
      var day = 1;

      for (var week = 0; week < 6; week += 1) {
        var row = document.createElement('tr');
        row.className = 'booking-calendar-field__week';
        row.dataset.week = String(week + 1);

        for (var column = 0; column < 7; column += 1) {
          var cell = document.createElement('td');
          cell.className = 'booking-calendar-field__cell';
          cell.dataset.weekday = String(column + 1);

          if ((week === 0 && column < startOffset) || day > daysInMonth) {
            cell.classList.add('booking-calendar-field__cell--empty');
            cell.setAttribute('aria-hidden', 'true');
            row.appendChild(cell);
            continue;
          }

          var date = new Date(visibleDate.getFullYear(), visibleDate.getMonth(), day);
          var label = formatDate(date);
          var button = document.createElement('button');
          var isDisabledWeekday = disabledWeekdays.indexOf(getIsoWeekday(date)) !== -1;
          var isOutsideWindow = isOutsideBookingWindow(date);
          var isDisabled = isDisabledWeekday || isOutsideWindow;
          var isSelected = selectedDate && toIsoDate(selectedDate) === toIsoDate(date);
          var isToday = toIsoDate(today) === toIsoDate(date);

          cell.classList.add('booking-calendar-field__cell--day');
          cell.classList.toggle('booking-calendar-field__cell--disabled', isDisabled);
          cell.classList.toggle('booking-calendar-field__cell--disabled-weekday', isDisabledWeekday);
          cell.classList.toggle('booking-calendar-field__cell--outside-booking-window', isOutsideWindow);
          cell.classList.toggle('booking-calendar-field__cell--selected', Boolean(isSelected));
          cell.classList.toggle('booking-calendar-field__cell--today', isToday);
          cell.dataset.date = toIsoDate(date);

          button.type = 'button';
          button.className = 'booking-calendar-field__day';
          button.classList.toggle('booking-calendar-field__day--disabled', isDisabled);
          button.classList.toggle('booking-calendar-field__day--disabled-weekday', isDisabledWeekday);
          button.classList.toggle('booking-calendar-field__day--outside-booking-window', isOutsideWindow);
          button.classList.toggle('booking-calendar-field__day--selected', Boolean(isSelected));
          button.classList.toggle('booking-calendar-field__day--today', isToday);
          button.textContent = String(day);
          button.dataset.date = toIsoDate(date);
          button.setAttribute('data-calendar-day', '');
          button.setAttribute('aria-label', isDisabled ? text(translations.unavailableDate, label) : text(translations.selectDate, label));
          button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');

          if (isToday) {
            button.setAttribute('aria-current', 'date');
          }

          if (isDisabled) {
            button.disabled = true;
          }

          cell.appendChild(button);
          row.appendChild(cell);
          day += 1;
        }

        grid.appendChild(row);

        if (day > daysInMonth) {
          break;
        }
      }
    }

    function render() {
      root.classList.toggle('booking-calendar-field--has-selection', Boolean(selectedDate));
      root.dataset.visibleMonth = pad(visibleDate.getMonth() + 1);
      root.dataset.visibleYear = String(visibleDate.getFullYear());

      if (selectedDate) {
        root.dataset.selectedDate = toIsoDate(selectedDate);
      } else {
        delete root.dataset.selectedDate;
      }

      renderControls();
      renderGrid();
    }

    previousButton.addEventListener('click', function () {
      visibleDate = new Date(visibleDate.getFullYear(), visibleDate.getMonth() - 1, 1);
      render();
    });

    nextButton.addEventListener('click', function () {
      visibleDate = new Date(visibleDate.getFullYear(), visibleDate.getMonth() + 1, 1);
      render();
    });

    if (dropdownsEnabled) {
      monthButton.addEventListener('click', function () {
        setButtonExpanded(monthButton, true);
        showSelect(monthSelect, monthButton);
      });

      yearButton.addEventListener('click', function () {
        setButtonExpanded(yearButton, true);
        showSelect(yearSelect, yearButton);
      });

      monthSelect.addEventListener('change', function () {
        visibleDate = new Date(visibleDate.getFullYear(), Number(monthSelect.value), 1);
        setButtonExpanded(monthButton, false);
        hideSelect(monthSelect, monthButton);
        render();
      });

      yearSelect.addEventListener('change', function () {
        visibleDate = new Date(Number(yearSelect.value), visibleDate.getMonth(), 1);
        setButtonExpanded(yearButton, false);
        hideSelect(yearSelect, yearButton);
        render();
      });

      [monthSelect, yearSelect].forEach(function (select) {
        select.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            var button = select === monthSelect ? monthButton : yearButton;
            setButtonExpanded(button, false);
            hideSelect(select, button);
          }
        });

        select.addEventListener('blur', function () {
          var button = select === monthSelect ? monthButton : yearButton;
          setButtonExpanded(button, false);
          hideSelect(select, button);
        });
      });
    }

    grid.addEventListener('click', function (event) {
      var button = event.target.closest('[data-calendar-day]');

      if (!button || button.disabled) {
        return;
      }

      selectedDate = parseIsoDate(button.dataset.date);
      input.value = button.dataset.date;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));

      if (status && selectedDate) {
        status.textContent = text(translations.selectedDate, formatDate(selectedDate));
      }

      render();
    });

    renderWeekdays();
    render();
  }

  function initialize() {
    document.querySelectorAll(calendarSelector).forEach(initializeCalendar);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
  } else {
    initialize();
  }
})();
