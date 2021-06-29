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

import {required, afterDate, endDateShouldBeAfterStartDate} from '../rules';

describe('core/util/validation/rules::required', () => {
  test('required::empty string', () => {
    const result = required('');
    expect(result).toBe('Required');
  });

  test('required::string only with space', () => {
    const result = required(' ');
    expect(result).toBe('Required');
  });

  test('required::string only with new line char', () => {
    const result = required('\n');
    expect(result).toBe('Required');
  });

  test('required::number', () => {
    const result = required(1);
    expect(result).toBeTruthy();
  });

  test('required::empty array', () => {
    const result = required([]);
    expect(result).toBe('Required');
  });

  test('required::array', () => {
    const result = required(['test']);
    expect(result).toBeTruthy();
  });

  test('required::object', () => {
    // @ts-expect-error
    let result = required({test: 'Object'});
    expect(result).toBeTruthy();

    // @ts-expect-error
    result = required(null);
    expect(result).toBe('Required');
  });

  test('required::unsupported type', () => {
    // @ts-expect-error
    const result = required(true);
    expect(result).toBe('Required');
  });
});

describe('core/util/validation/rules::afterDate', () => {
  test('afterDate::empty string', () => {
    let result = afterDate('', '');
    expect(result).toBeTruthy();

    result = afterDate('2021-06-28', '');
    expect(result).toBeTruthy();
  });

  test('afterDate::valid', () => {
    const result = afterDate('2021-06-29', '2021-06-28');
    expect(result).toBeTruthy();
  });

  test('afterDate::invalid', () => {
    const result = afterDate('2021-06-28', '2021-06-29');
    expect(result).toBeFalsy();
  });

  test('afterDate::equal', () => {
    const result = afterDate('2021-06-28', '2021-06-28');
    expect(result).toBeFalsy();
  });

  test('afterDate::invalid date format', () => {
    const result = afterDate('2021-06-29', '2021-06-28', 'yyyy/MM/dd');
    expect(result).toBeFalsy();
  });

  test('afterDate::valid date format', () => {
    const result = afterDate('2021/06/29', '2021/06/28', 'yyyy/MM/dd');
    expect(result).toBeTruthy();
  });
});

describe('core/util/validation/rules::endDateShouldBeAfterStartDate', () => {
  test('endDateShouldBeAfterStartDate::empty string', () => {
    let result = endDateShouldBeAfterStartDate('')('');
    expect(result).toBeTruthy();

    result = endDateShouldBeAfterStartDate('2021-06-28')('');
    expect(result).toBeTruthy();
  });

  test('endDateShouldBeAfterStartDate::valid', () => {
    const result = endDateShouldBeAfterStartDate('2021-06-28')('2021-06-29');
    expect(result).toBeTruthy();
  });

  test('endDateShouldBeAfterStartDate::invalid case', () => {
    const result = endDateShouldBeAfterStartDate('2021-06-29')('2021-06-28');
    expect(result).toBe('End date should be after start date');
  });

  test('endDateShouldBeAfterStartDate::valid (start date as function)', () => {
    const result = endDateShouldBeAfterStartDate(() => '2021-06-28')(
      '2021-06-29',
    );
    expect(result).toBeTruthy();
  });

  test('endDateShouldBeAfterStartDate::invalid case (custom message)', () => {
    const result = endDateShouldBeAfterStartDate(
      '2021-06-29',
      'To date should be after From date',
    )('2021-06-28');
    expect(result).toBe('To date should be after From date');
  });

  test('endDateShouldBeAfterStartDate::invalid date format', () => {
    const result = endDateShouldBeAfterStartDate(
      '2021-06-29',
      undefined,
      'yyyy/MM/dd',
    )('2021-06-28');
    expect(result).toBe('End date should be after start date');
  });

  test('endDateShouldBeAfterStartDate::valid date format', () => {
    const result = endDateShouldBeAfterStartDate(
      '2021/06/29',
      // @ts-expect-error
      null,
      'yyyy/MM/dd',
    )('2021/06/28');
    expect(result).toBe('End date should be after start date');
  });
});
