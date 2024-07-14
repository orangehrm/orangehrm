/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

import {
  compareTime,
  diffInDays,
  formatDate,
  isAfter,
  isBefore,
  isEqual,
  parseDate,
} from '../helper/datefns';
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';

const translate = translatorFactory();

export type File = {
  name: string;
  type: string;
  size: number;
  base64: string;
};

/**
 * @param {string|number|Array} value
 * @returns {boolean|string}
 */
export const required = function (
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
export const shouldNotExceedCharLength = function (charLength: number) {
  return function (value: string): boolean | string {
    return (
      !value ||
      new String(value).length <= charLength ||
      translate('general.should_be_less_n_characters', {amount: charLength})
    );
  };
};

export const validDateFormat = function (
  displayFormat = 'yyyy-mm-dd',
  dateFormat = 'yyyy-MM-dd',
) {
  return function (value: string): boolean | string {
    if (!value) return true;
    const parsed = parseDate(value, dateFormat);
    return parsed
      ? true
      : translate('general.should_be_a_valid_date_in_x_format', {
          format: displayFormat,
        });
  };
};

export const shouldBeCurrentOrPreviousDate = function () {
  return function (value: string): boolean | string {
    if (!value) return true;
    const dateFormat = 'yyyy-MM-dd';
    const currentDate = formatDate(new Date(), dateFormat) || '';
    const isValid = diffInDays(value, currentDate, dateFormat);
    return isValid > 0
      ? true
      : translate('recruitment.should_be_current_date_previous_date');
  };
};

export const validTimeFormat = function (value: string): boolean | string {
  if (!value) return true;
  const parsed = parseDate(value, 'HH:mm');
  return parsed
    ? true
    : translate('general.should_be_a_valid_date_in_hh:mm_format');
};

export const max = function (maxValue: number) {
  return function (value: string): boolean | string {
    return (
      Number.isNaN(parseFloat(value)) ||
      parseFloat(value) < maxValue ||
      translate('general.should_be_less_than_n', {amount: maxValue})
    );
  };
};

export const digitsOnly = function (value: string): boolean | string {
  return (
    value == '' ||
    (/^\d+$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    translate('general.should_be_a_number')
  );
};

export const numericOnly = function (value: string): boolean | string {
  return (
    value == '' ||
    (/^\d+$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    translate('general.should_be_a_numeric_value')
  );
};

export const digitsOnlyWithDecimalPoint = function (
  value: string,
): boolean | string {
  return (
    value == '' ||
    (/^\d*\.?\d*$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    translate('general.should_be_a_number')
  );
};

export const digitsOnlyWithDecimalPointAndMinusSign = function (
  value: string,
): boolean | string {
  return (
    value == '' ||
    (/^-?\d*\.?\d*$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    translate('general.should_be_a_number')
  );
};

/**
 * Check whether date1 is before date2
 * @param {string} date1
 * @param {string} date2
 * @param {string} dateFormat
 */
export const beforeDate = function (
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
export const afterDate = function (
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
export const sameDate = function (
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
  startDate: string | (() => string),
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
export const beforeTime = function (
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
export const afterTime = function (
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
export const sameTime = function (
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
  startTime: string | (() => string),
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
export const maxFileSize = function (size: number) {
  return function (file: File): boolean | string {
    return (
      file === null ||
      (file.size && file.size <= size) ||
      translate('general.attachment_size_exceeded')
    );
  };
};

export const validFileTypes = function (fileTypes: string[]) {
  return function (file: File): boolean | string {
    return (
      file === null ||
      (file && fileTypes.findIndex((item) => item === file.type) > -1) ||
      translate('general.file_type_not_allowed')
    );
  };
};

export const validEmailFormat = function (value: string): boolean | string {
  return (
    !value ||
    /^[a-zA-Z0-9.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/.test(
      value,
    ) ||
    translate('general.expected_email_address_format_not_matched')
  );
};

export const validPhoneNumberFormat = function (
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
  endDate: string | (() => string),
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

export const maxCurrency = function (maxValue: number) {
  return function (value: string): boolean | string {
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
  endTime: string | (() => string),
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
export const shouldNotLessThanCharLength = function (charLength: number) {
  return function (value: string): boolean | string {
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
export const maxValueShouldBeGreaterThanMinValue = (
  minValue: string | (() => string),
  message?: string,
) => {
  return (value: string): boolean | string => {
    const resolvedMinValue =
      typeof minValue === 'function' ? minValue() : minValue;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.should_be_higher_than_minimum_value');
    // If the minimum is not given, null or 0 => return true
    // If the value is not given, then return true only if the minimum is not given, null or 0
    if (resolvedMinValue === null) return true;
    if (resolvedMinValue === undefined) return true;
    if (resolvedMinValue === '' || (resolvedMinValue === '' && value === ''))
      return true;
    if (resolvedMinValue === '0' || (resolvedMinValue === '0' && value === '0'))
      return true;
    return parseFloat(resolvedMinValue) < parseFloat(value) || resolvedMessage;
  };
};

/**
 * @param {string | function} maxValue
 * @param {string|undefined} message
 */
export const minValueShouldBeLowerThanMaxValue = (
  maxValue: string | (() => string),
  message?: string,
) => {
  return (value: string): boolean | string => {
    const resolvedMaxValue =
      typeof maxValue === 'function' ? maxValue() : maxValue;
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.should_be_lower_than_maximum_value');
    if (resolvedMaxValue === null || value === null) return true;
    if (resolvedMaxValue === undefined || value === undefined) return true;
    if (resolvedMaxValue === '' || value === '') return true;
    if (resolvedMaxValue === '0' || value === '0') return true;
    return parseFloat(resolvedMaxValue) > parseFloat(value) || resolvedMessage;
  };
};

/**
 * @param {number} minValue
 * @param {number} maxValue
 * @param {string|undefined} message
 */
export const numberShouldBeBetweenMinAndMaxValue = (
  minValue: number,
  maxValue: number,
  message?: string,
) => {
  return (value: string): boolean | string => {
    const resolvedMessage =
      typeof message === 'string'
        ? message
        : translate('general.should_be_a_number_between_min_and_max', {
            min: minValue,
            max: maxValue,
          });
    return (
      (digitsOnly(value) === true &&
        parseFloat(value) >= minValue &&
        parseFloat(value) <= maxValue) ||
      resolvedMessage
    );
  };
};

/**
 * Validate #rrggbb & #rgb hex strings
 * @param {string} value hex string
 * @returns {boolean|string}
 */
export const validHexFormat = function (value: string): boolean | string {
  if (!value) return true;
  return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i.test(value)
    ? true
    : translate('general.invalid');
};

/**
 * Validate image dimensions
 * @param {number} aspectRatio width/height
 * @param {number} tolerance
 * @returns {Promise<boolean|string>}
 */
export const imageShouldHaveDimensions = function (
  aspectRatio: number,
  tolerance = 0.1,
) {
  return function (file: File | null): Promise<boolean | string> {
    return new Promise((resolve) => {
      if (file === null || file.type === 'image/svg+xml') return resolve(true);
      const image = new Image();
      image.src = `data:${file.type};base64, ${file.base64}`;
      image.decode().then(() => {
        if (Math.abs(image.width / image.height - aspectRatio) < tolerance) {
          resolve(true);
        } else {
          resolve(translate('general.incorrect_dimensions'));
        }
      });
    });
  };
};

export const greaterThanOrEqual = function (
  minValue: number,
  message?: string,
) {
  const resolvedMessage =
    typeof message === 'string'
      ? message
      : translate('general.greater_than_or_equal_to_n', {minValue: minValue});
  return function (value: string): boolean | string {
    if (value === null || value === '') return true;
    if (digitsOnlyWithDecimalPointAndMinusSign(value) !== true) {
      return resolvedMessage;
    }
    return parseFloat(value) >= minValue || resolvedMessage;
  };
};

export const lessThanOrEqual = function (maxValue: number, message?: string) {
  const resolvedMessage =
    typeof message === 'string'
      ? message
      : translate('general.less_than_or_equal_to_n', {maxValue: maxValue});
  return function (value: string): boolean | string {
    if (value === null || value === '') return true;
    if (digitsOnlyWithDecimalPointAndMinusSign(value) !== true) {
      return resolvedMessage;
    }
    return parseFloat(value) <= maxValue || resolvedMessage;
  };
};

export const validLangString = function (value: string) {
  if (value === null || value === '') {
    return true;
  }
  return value.split('').reduce((accumulator, currentValue) => {
    if (currentValue === '{') accumulator++;
    if (currentValue === '}') accumulator--;
    return accumulator;
  }, 0) !== 0
    ? translate('general.invalid')
    : true;
};

/**
 * Validate autocomplete selection
 * @param {string|object|null} value
 * @returns {boolean|string}
 */
export const validSelection = function (value: string | object | null) {
  return typeof value === 'string' ? translate('general.invalid') : true;
};

export const validHostnameFormat = function (value: string): boolean | string {
  let fqdnRegex;

  // If string contains any letters, treat the string as a hostname. else ip address
  if (/\p{L}/u.test(value)) {
    fqdnRegex =
      /^([\p{L}\p{N}\p{S}\-.])+(\.?([\p{L}\p{N}]|xn--[\p{L}\p{N}-]+)+\.?)(:[0-9]+)?$/gu;
  } else {
    fqdnRegex =
      /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;
  }

  return !value || fqdnRegex.test(value) || translate('general.invalid');
};

export const validPortRange = function (
  charLength: number,
  rangeFrom: number,
  rangeTo: number,
) {
  return function (value: string): boolean | string {
    return (
      !value ||
      (/^\d+$/.test(value) &&
        !Number.isNaN(parseFloat(value)) &&
        String(value).length <= charLength &&
        parseInt(value) >= rangeFrom &&
        parseInt(value) <= rangeTo) ||
      translate('general.enter_valid_port_between_a_to_b', {
        minValue: rangeFrom,
        maxValue: rangeTo,
      })
    );
  };
};

/**
 * Validate url to be a valid youtube video
 * @param {string} value url string
 * @returns
 */
export const validVideoURL = function (value: string): boolean | string {
  return (
    !value ||
    /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:shorts\/|embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(\?\S*)?$/.test(
      value,
    ) ||
    translate('general.invalid_video_url_message')
  );
};

export const digitsOnlyWithTwoDecimalPoints = function (
  value: string,
): boolean | string {
  return (
    value == '' ||
    /^\d+?(?:\.\d{1,2})?$/.test(value) ||
    translate('claim.should_be_a_valid_number')
  );
};
