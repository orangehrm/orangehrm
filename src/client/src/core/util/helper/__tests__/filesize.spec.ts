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

import {convertFilesizeToString} from '../filesize';

describe('core/util/helper/filesize', () => {
  test('convertFilesizeToString::convert 100 bytes', () => {
    const result = convertFilesizeToString(100);
    expect(result).toBe('100 B');
  });

  test('convertFilesizeToString::convert 1000 bytes', () => {
    const result = convertFilesizeToString(1000);
    expect(result).toBe('1 kB');
  });

  test('convertFilesizeToString::convert 1024 bytes', () => {
    const result = convertFilesizeToString(1024);
    expect(result).toBe('1 kB');
  });

  test('convertFilesizeToString::convert 1100 bytes', () => {
    const result = convertFilesizeToString(1100);
    expect(result).toBe('1 kB');
  });

  test('convertFilesizeToString::convert 1500 bytes', () => {
    const result = convertFilesizeToString(1500, 2);
    expect(result).toBe('1.50 kB');
  });

  test('convertFilesizeToString::convert 1800 bytes', () => {
    const result = convertFilesizeToString(1800, 2);
    expect(result).toBe('1.80 kB');
  });

  test('convertFilesizeToString::convert 1000000 bytes', () => {
    const result = convertFilesizeToString(999999, 2);
    expect(result).toBe('1000.00 kB');
  });

  test('convertFilesizeToString::convert 1024 kB', () => {
    const result = convertFilesizeToString(1048576, 2);
    expect(result).toBe('1.05 MB');
  });

  test('convertFilesizeToString::convert 1024 MB', () => {
    const result = convertFilesizeToString(1024 * 1024 * 1024, 2);
    expect(result).toBe('1.07 GB');
  });

  test('convertFilesizeToString::convert 1024 bytes (string type)', () => {
    const result = convertFilesizeToString('1024', 2);
    expect(result).toBe('1.02 kB');
  });

  test('convertFilesizeToString::convert 1024 bytes (without suffix)', () => {
    const result = convertFilesizeToString(1024, 2, false);
    expect(result).toBe('1.02');
  });

  test('convertFilesizeToString::convert 1024 kB', () => {
    const result = convertFilesizeToString(1000 * 1000, 2);
    expect(result).toBe('1.00 MB');
  });

  test('convertFilesizeToString::convert 1024 MB', () => {
    const result = convertFilesizeToString(1000 * 1000 * 1000, 2);
    expect(result).toBe('1.00 GB');
  });
});
