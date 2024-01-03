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

/**
 * @param {string|number|Array} value
 * @returns {boolean|string}
 */
export const required = function (
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  value: string | number | Array<any>,
): boolean | string {
  if (typeof value === 'string') {
    return (!!value && value.trim() !== '') || 'Required';
  } else if (typeof value === 'number') {
    return !Number.isNaN(value) || 'Required';
  } else if (Array.isArray(value)) {
    return (!!value && value.length !== 0) || 'Required';
  } else if (typeof value === 'object') {
    return value !== null || 'Required';
  } else {
    return 'Required';
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
      `Should not exceed ${charLength} characters`
    );
  };
};

export const validRange = function (
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
      `Enter a valid port number: ${rangeFrom}-${rangeTo}`
    );
  };
};

export const digitsOnly = function (value: string): boolean | string {
  return (
    value == '' ||
    (/^\d+$/.test(value) && !Number.isNaN(parseFloat(value))) ||
    'Should be a number'
  );
};

export const validEmailFormat = function (value: string): boolean | string {
  return (
    !value ||
    /^[a-zA-Z0-9.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/.test(
      value,
    ) ||
    'Expected format: admin@example.com'
  );
};

export const validPhoneNumberFormat = function (
  value: string,
): boolean | string {
  return (
    !value ||
    /^[0-9+\-/() ]+$/.test(value) ||
    'Allows numbers and only + - / ( )'
  );
};

/**
 * @param {number} charLength
 */
export const shouldNotLessThanCharLength = function (charLength: number) {
  return function (value: string): boolean | string {
    return (
      !value ||
      String(value).length >= charLength ||
      `Should be at least ${charLength} characters`
    );
  };
};

export const shouldNotContainSpecialChars = (message?: string) => {
  const resolvedMessage =
    message ?? 'Allows alphanumeric characters and only _';
  return function (value: string): boolean | string {
    return !value || /^[a-zA-Z0-9_]*$/.test(value) || resolvedMessage;
  };
};
