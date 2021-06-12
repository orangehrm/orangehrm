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
 * @param {string} value
 * @returns {boolean|string}
 */
export const required = function(value: string): boolean | string {
  return (!!value && value.trim() !== '') || 'Required';
};

/**
 * @param {number} charLength
 */
export const shouldNotExceedCharLength = function(charLength: number) {
  return function(value: string): boolean | string {
    return (
      !value ||
      value.length <= charLength ||
      `Should not exceed ${charLength} characters`
    );
  };
};

export const validDateFormat = function(dateFormat: string) {
  return function(value: string): boolean | string {
    const parsed = parseDate(value, dateFormat);
    return parsed ? true : `Should be a valid date in ${dateFormat} format`;
  };
};

export const beforeDate = function(
  dateFormat: string,
  valueToCompare: string,
  message: string,
) {
  return function(value: string): boolean | string {
    const origin = parseDate(value, dateFormat);
    const reference = parseDate(valueToCompare, dateFormat);
    if (origin && reference) {
      return isBefore(origin, reference) ? true : message;
    }
    return message;
  };
};

export const afterDate = function(
  dateFormat: string,
  valueToCompare: string,
  message: string,
) {
  return function(value: string): boolean | string {
    const origin = parseDate(value, dateFormat);
    const reference = parseDate(valueToCompare, dateFormat);
    if (origin && reference) {
      return isAfter(origin, reference) ? true : message;
    }
    return message;
  };
};
