/**
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

const BYTE = 1;
const KILO_BYTE = 1024;
const MEGA_BYTE = 1048576; // 1024 * 1024
const GIGA_BYTE = 1073741824; // 1024 * 1024 * 1024

/**
 * @param {string|number} value
 * @param {number|null} digits
 * @param {boolean} withSuffix
 */
export const convertFilesizeToString = function(
  value: string | number,
  digits?: number,
  withSuffix = true,
): string {
  let divisor = BYTE;
  let suffix = 'B';
  let filesize;
  if (typeof value === 'number') {
    filesize = value;
  } else {
    filesize = parseInt(value, 10);
  }

  if (filesize >= GIGA_BYTE) {
    divisor = GIGA_BYTE;
    suffix = 'GB';
  } else if (filesize >= MEGA_BYTE) {
    divisor = MEGA_BYTE;
    suffix = 'MB';
  } else if (filesize >= KILO_BYTE) {
    divisor = KILO_BYTE;
    suffix = 'kB';
  }

  return (
    (filesize / divisor).toFixed(digits) + (withSuffix ? ' ' + suffix : '')
  );
};
