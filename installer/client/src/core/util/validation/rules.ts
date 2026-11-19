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

// Removes what PHP's trim() removes (not other Unicode spaces), as the server trims the value that way
const trimLikeServer = function (value: string): string {
  const trimmed = ' \t\n\r\0\x0B';
  let start = 0;
  let end = value.length;
  while (start < end && trimmed.includes(value[start])) {
    start++;
  }
  while (end > start && trimmed.includes(value[end - 1])) {
    end--;
  }
  return value.slice(start, end);
};

// Host name: labels of letters, digits and inner hyphens, up to 63 characters each and 253 in all,
// optionally followed by a single dot
const isHostName = function (host: string): boolean {
  const name = host.endsWith('.') ? host.slice(0, -1) : host;
  return (
    name.length > 0 &&
    name.length <= 253 &&
    name
      .split('.')
      .every((label) => /^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i.test(label))
  );
};

// IPv6 address in hexadecimal groups; the server does not accept an embedded IPv4 address or a zone
const isIpv6Address = function (address: string): boolean {
  const halves = address.split('::');
  if (halves.length > 2) {
    return false;
  }
  const groups = ([] as string[]).concat(
    ...halves.map((half) => (half === '' ? [] : half.split(':'))),
  );
  if (!groups.every((group) => /^[0-9a-f]{1,4}$/i.test(group))) {
    return false;
  }
  return halves.length === 2 ? groups.length <= 7 : groups.length === 8;
};

/**
 * Application base URL, checked as the server checks it (InstanceBaseUrl::normalize): an absolute http(s) URL
 * with a host name or bracketed IPv6 address, an optional port (1-65535, may be left empty) and a path; no
 * credentials, spaces, non-ASCII characters, query or fragment. Scheme and host are case-insensitive. The server
 * validates again.
 */
export const validBaseUrl = function (value: string): boolean | string {
  const message = 'Expected format: https://hr.example.com/orangehrm/index.php';
  const url = trimLikeServer(value ?? '');
  if (url === '') {
    return true;
  }
  // Path characters: those PHP's FILTER_VALIDATE_URL allows, without ? and #
  const match = url.match(
    /^https?:\/\/(\[[^\]]*\]|[^/:[\]]*)(?::([0-9]*))?(\/[A-Za-z0-9$\-_.+!*'(),{}|\\^~[\]`<>%";/:@&=]*)?$/i,
  );
  if (!match) {
    return message;
  }
  const [, host, port] = match;
  const validHost = host.startsWith('[')
    ? isIpv6Address(host.slice(1, -1))
    : isHostName(host);
  const validPort =
    port === undefined ||
    port === '' ||
    (port.length <= 5 && Number(port) >= 1 && Number(port) <= 65535);
  return (validHost && validPort) || message;
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
