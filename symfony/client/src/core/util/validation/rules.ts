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

import {parseDate, isBefore, isAfter} from '../helper/datefns';

/**
 * @param {string|number|Array} value
 * @returns {boolean|string}
 */

export const required = function(
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  value: string | number | Array<any>,
): boolean | string {
  if (typeof value === 'string') {
    return (!!value && value.trim() !== '') || 'Required';
  } else if (typeof value === 'number') {
    return Number.isNaN(value) || 'Required';
  } else if (Array.isArray(value)) {
    return (!!value && value.length !== 0) || 'Required';
  } else {
    return 'Required';
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
      `Should not exceed ${charLength} characters`
    );
  };
};

export const validDateFormat = function(dateFormat: string) {
  return function(value: string): boolean | string {
    if (!value) return true;
    const parsed = parseDate(value, dateFormat);
    return parsed ? true : `Should be a valid date in ${dateFormat} format`;
  };
};

export const max = function(maxValue: number) {
  return function(value: string): boolean | string {
    return (
      Number.isNaN(parseFloat(value)) ||
      parseFloat(value) < maxValue ||
      `Should be less than ${maxValue}`
    );
  };
};

export const digitsOnly = function(value: string): boolean | string {
  return (
    value == '' ||
    (/^\d+$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    'Should be a number'
  );
};

export const beforeDate = function(
  dateFormat: string,
  date1: string,
  date2: string,
) {
  // Skip assersion on unset values
  if (!date1 || !date2) {
    return true;
  }
  return isBefore(date1, date2, dateFormat);
};

export const afterDate = function(
  dateFormat: string,
  date1: string,
  date2: string,
) {
  // Skip assersion on unset values
  if (!date1 || !date2) {
    return true;
  }
  return isAfter(date1, date2, dateFormat);
};
