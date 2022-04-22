/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

import {
  compareTime,
  isAfter,
  isBefore,
  isEqual,
  parseDate,
} from '../helper/datefns';
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';

const translate = translatorFactory();

/**
 * @param {string|number|Array} value
 * @returns {boolean|string}
 */
export const required = function(
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  value: string | number | Array<any>,
): boolean | string {
  if (typeof value === 'string') {
    return (!!value && value.trim() !== '') || translate('general.required');
  } else if (typeof value === 'number') {
    return !Number.isNaN(value) || 'general.required';
  } else if (Array.isArray(value)) {
    return (!!value && value.length !== 0) || translate('general.required');
  } else if (typeof value === 'object') {
    return value !== null || translate('general.required');
  } else {
    return translate('general.required');
  }
};

/**
 * @param {number} charLength
 */
export const shouldNotExceedCharLength = function(charLength: number) {
  return function(value: string): boolean | string {
    return (
      !value ||
      new String(value).length <= charLength ||
      translate('general.should_be_less_n_characters', {amount: charLength})
    );
  };
};

export const validDateFormat = function(dateFormat = 'yyyy-MM-dd') {
  return function(value: string): boolean | string {
    if (!value) return true;
    const parsed = parseDate(value, dateFormat);
    return parsed
      ? true
      : translate('general.should_be_a_valid_date_in_x_format', {
          format: dateFormat,
        });
  };
};

export const validTimeFormat = function(value: string): boolean | string {
  if (!value) return true;
  const parsed = parseDate(value, 'HH:mm');
  return parsed
    ? true
    : translate('general.should_be_a_valid_date_in_hh:mm_format');
};

export const max = function(maxValue: number) {
  return function(value: string): boolean | string {
    return (
      Number.isNaN(parseFloat(value)) ||
      parseFloat(value) < maxValue ||
      translate('general.should_be_less_than_n', {amount: maxValue})
    );
  };
};

export const digitsOnly = function(value: string): boolean | string {
  return (
    value == '' ||
    (/^\d+$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    translate('general.should_be_a_number')
  );
};

export const digitsOnlyWithDecimalPoint = function(
  value: string,
): boolean | string {
  return (
    value == '' ||
    (/^\d*\.?\d*$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    translate('general.should_be_a_number')
  );
};

/**
 * Check whether date1 is before date2
 * @param {string} date1
 * @param {string} date2
 * @param {string} dateFormat
 */
export const beforeDate = function(
  date1: string,
  date2: string,
  dateFormat = 'yyyy-MM-dd',
) {
  // Skip assertion on unset values
  if (!date1 || !date2) {
    return true;
  }
  return isBefore(date1, date2, dateFormat);
};

/**
 * Check whether date1 is after date2
 * @param {string} date1
 * @param {string} date2
 * @param {string} dateFormat
 */
export const afterDate = function(
  date1: string,
  date2: string,
  dateFormat = 'yyyy-MM-dd',
) {
  // Skip assertion on unset values
  if (!date1 || !date2) {
    return true;
  }
  return isAfter(date1, date2, dateFormat);
};

/**
 * Check whether date1 is same as date2
 * @param {string} date1
 * @param {string} date2
 * @param {string} dateFormat
 */
export const sameDate = function(
  date1: string,
  date2: string,
  dateFormat = 'yyyy-MM-dd',
) {
  // Skip assertion on unset values
  if (!date1 || !date2) {
    return true;
  }
  return isEqual(date1, date2, dateFormat);
};

/**
 * @param {string} startDate
 * @param {string|undefined} message
 * @param {object} options
 */
export const endDateShouldBeAfterStartDate = (
  startDate: string | Function,
  message?: string,
  options: {
    allowSameDate?: boolean;
    dateFormat?: string;
  } = {
    allowSameDate: false,
    dateFormat: 'yyyy-MM-dd',
  },
) => {
  return (value: string): boolean | string => {
    const resolvedStartDate =
      typeof startDate === 'function' ? startDate() : startDate;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.end_date_should_be_after_start_date');
    if (options.allowSameDate) {
      return (
        sameDate(value, resolvedStartDate) ||
        afterDate(value, resolvedStartDate, options.dateFormat) ||
        resolvedMessage
      );
    } else {
      return (
        afterDate(value, resolvedStartDate, options.dateFormat) ||
        resolvedMessage
      );
    }
  };
};

/**
 * Check whether time1 is before time2
 * @param {string} time1
 * @param {string} time2
 * @param {string} timeFormat
 */
export const beforeTime = function(
  time1: string,
  time2: string,
  timeFormat = 'yyyy-MM-dd',
) {
  // Skip assertion on unset values
  if (!time1 || !time2) {
    return true;
  }
  return compareTime(time1, time2, timeFormat) === 1;
};

/**
 * Check whether time1 is after time2
 * @param {string} time1
 * @param {string} time2
 * @param {string} timeFormat
 */
export const afterTime = function(
  time1: string,
  time2: string,
  timeFormat = 'HH:mm',
) {
  // Skip assertion on unset values
  if (!time1 || !time2) {
    return true;
  }
  return compareTime(time1, time2, timeFormat) === -1;
};

/**
 * Check whether time1 is equal time2
 * @param {string} time1
 * @param {string} time2
 * @param {string} timeFormat
 */
export const sameTime = function(
  time1: string,
  time2: string,
  timeFormat = 'HH:mm',
) {
  // Skip assertion on unset values
  if (!time1 || !time2) {
    return true;
  }
  return compareTime(time1, time2, timeFormat) === 0;
};

/**
 * @param {string} startTime
 * @param {string|undefined} message
 * @param {object} options
 */
export const endTimeShouldBeAfterStartTime = (
  startTime: string | Function,
  message?: string,
  options: {
    allowSameTime?: boolean;
    timeFormat?: string;
  } = {
    allowSameTime: false,
    timeFormat: 'HH:mm',
  },
) => {
  return (value: string): boolean | string => {
    const resolvedStartTime =
      typeof startTime === 'function' ? startTime() : startTime;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.end_time_should_be_after_start_time');
    if (options.allowSameTime) {
      return (
        sameTime(value, resolvedStartTime) ||
        afterTime(value, resolvedStartTime, options.timeFormat) ||
        resolvedMessage
      );
    } else {
      return (
        afterTime(value, resolvedStartTime, options.timeFormat) ||
        resolvedMessage
      );
    }
  };
};

/**
 * @param {number} size - File size in bytes
 */
export const maxFileSize = function(size: number) {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  return function(file: any): boolean | string {
    return (
      file === null ||
      (file.size && file.size <= size) ||
      translate('general.attachment_size_exceeded')
    );
  };
};

export const validFileTypes = function(fileTypes: string[]) {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  return function(file: any): boolean | string {
    return (
      file === null ||
      (file && fileTypes.findIndex(item => item === file.type) > -1) ||
      translate('general.file_type_not_allowed')
    );
  };
};

export const validEmailFormat = function(value: string): boolean | string {
  return (
    !value ||
    /^[a-zA-Z0-9.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/.test(
      value,
    ) ||
    translate('general.expected_email_address_format_not_matched')
  );
};

export const validPhoneNumberFormat = function(
  value: string,
): boolean | string {
  return (
    !value ||
    /^[0-9+\-/() ]+$/.test(value) ||
    translate('general.allows_phone_numbers_only')
  );
};

/**
 * @param {string} endDate
 * @param {string|undefined} message
 * @param {object} options
 */
export const startDateShouldBeBeforeEndDate = (
  endDate: string | Function,
  message?: string,
  options: {
    allowSameDate?: boolean;
    dateFormat?: string;
  } = {
    allowSameDate: false,
    dateFormat: 'yyyy-MM-dd',
  },
) => {
  return (value: string): boolean | string => {
    const resolvedEndDate = typeof endDate === 'function' ? endDate() : endDate;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.start_date_should_be_before_end_date');
    if (options.allowSameDate) {
      return (
        sameDate(value, resolvedEndDate) ||
        beforeDate(value, resolvedEndDate, options.dateFormat) ||
        resolvedMessage
      );
    } else {
      return (
        beforeDate(value, resolvedEndDate, options.dateFormat) ||
        resolvedMessage
      );
    }
  };
};

export const maxCurrency = function(maxValue: number) {
  return function(value: string): boolean | string {
    return (
      Number.isNaN(parseFloat(value)) ||
      parseFloat(value) < maxValue ||
      translate('general.should_be_less_than_n', {
        amount: maxValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','),
      })
    );
  };
};

/**
 * @param {string} endTime
 * @param {string|undefined} message
 * @param {object} options
 */
export const startTimeShouldBeBeforeEndTime = (
  endTime: string | Function,
  message?: string,
  options: {
    allowSameTime?: boolean;
    timeFormat?: string;
  } = {
    allowSameTime: false,
    timeFormat: 'HH:mm',
  },
) => {
  return (value: string): boolean | string => {
    const resolvedEndTime = typeof endTime === 'function' ? endTime() : endTime;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.start_time_should_be_before_end_time');
    if (options.allowSameTime) {
      return (
        sameTime(value, resolvedEndTime) ||
        beforeTime(value, resolvedEndTime, options.timeFormat) ||
        resolvedMessage
      );
    } else {
      return (
        beforeTime(value, resolvedEndTime, options.timeFormat) ||
        resolvedMessage
      );
    }
  };
};

/**
 * @param {number} charLength
 */
export const shouldNotLessThanCharLength = function(charLength: number) {
  return function(value: string): boolean | string {
    return (
      !value ||
      String(value).length >= charLength ||
      translate('general.should_be_least_n_characters', {amount: charLength})
    );
  };
};

/**
 * @param {string | function} minValue
 * @param {string|undefined} message
 */
export const minValueShouldBeLowerThanMaxValue = (
  minValue: string | Function,
  message?: string,
) => {
  return (value: string): boolean | string => {
    const resolvedMinValue =
      typeof minValue === 'function' ? minValue() : minValue;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.should_be_higher_than_minimum_value');
    if (resolvedMinValue === null || value === null) return true;
    if (resolvedMinValue === undefined || value === undefined) return true;
    if (resolvedMinValue === '' || value === '') return true;
    if (resolvedMinValue === '0' || value === '0') return true;
    return parseFloat(resolvedMinValue) < parseFloat(value) || resolvedMessage;
  };
};
