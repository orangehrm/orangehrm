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

import {
  required,
  afterDate,
  endDateShouldBeAfterStartDate,
  validEmailFormat,
  validPhoneNumberFormat,
  endTimeShouldBeAfterStartTime,
  startDateShouldBeBeforeEndDate,
  startTimeShouldBeBeforeEndTime,
  minValueShouldBeLowerThanMaxValue,
  digitsOnly,
  digitsOnlyWithDecimalPoint,
} from '../rules';

jest.mock('@/core/plugins/i18n/translate', () => {
  const t = (langString: string) => {
    const mockStrings: {[key: string]: string} = {
      'general.required': 'Required',
      'general.to_date_should_be_after_from_date':
        'To date should be after From date',
      'general.allows_phone_numbers_only': 'Allows numbers and only + - / ( )',
      'general.start_time_should_be_before_end_time':
        'Start time should be before end time',
      'general.end_time_should_be_after_start_time':
        'End time should be after start time',
      'general.start_date_should_be_before_end_date':
        'Start date should be before end date',
      'general.end_date_should_be_after_start_date':
        'End date should be after start date',
      'general.expected_email_address_format_not_matched':
        'Expected format: admin@example.com',
      'general.should_be_a_number': 'Should be a number',
    };
    return mockStrings[langString];
  };
  return {translate: jest.fn(() => t)};
});

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
    const result = endDateShouldBeAfterStartDate('2021-06-29', undefined, {
      dateFormat: 'yyyy/MM/dd',
    })('2021-06-28');
    expect(result).toBe('End date should be after start date');
  });

  test('endDateShouldBeAfterStartDate::valid date format', () => {
    const result = endDateShouldBeAfterStartDate('2021/06/29', undefined, {
      dateFormat: 'yyyy/MM/dd',
    })('2021/06/28');
    expect(result).toBe('End date should be after start date');
  });

  test('endDateShouldBeAfterStartDate:: should allow same day as start date when allowSameDate is true', () => {
    const result = endDateShouldBeAfterStartDate('2021-08-05', undefined, {
      allowSameDate: true,
    })('2021-08-05');
    expect(result).toEqual(true);
  });

  test('endDateShouldBeAfterStartDate:: should not allow invalid date when allowSameDate is true', () => {
    const result = endDateShouldBeAfterStartDate('2021-08-05', undefined, {
      allowSameDate: true,
    })('2021-08-03');
    expect(result).toEqual('End date should be after start date');
  });
});

describe('core/util/validation/rules::validEmailFormat', () => {
  test('validEmailFormat:invalidEmail', () => {
    const result = validEmailFormat('abcd');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:noAtSign', () => {
    const result = validEmailFormat('deviohrm.com');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:noUsername', () => {
    const result = validEmailFormat('@ohrm.com');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:noFullStopForDomain', () => {
    const result = validEmailFormat('devi@ohrmcom');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:fullStopWithNoDomain', () => {
    const result = validEmailFormat('devi@ohrm.');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:fullStopAfterDomain', () => {
    const result = validEmailFormat('devi@ohrm.com.');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:multipleFullStops', () => {
    const result = validEmailFormat('devi@ohrm..com');
    expect(result).toBe('Expected format: admin@example.com');
  });

  test('validEmailFormat:validEmail', () => {
    const result = validEmailFormat('devi@ohrm.com');
    expect(result).toStrictEqual(true);
  });

  test('validEmailFormat:validEmail2', () => {
    const result = validEmailFormat('devi@ohrm.co.uk');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::validPhoneNumberFormat', () => {
  test('validPhoneNumberFormat::number', () => {
    const result = validPhoneNumberFormat('1234563');
    expect(result).toBeTruthy();
  });

  test('validPhoneNumberFormat::numberWithStar', () => {
    const result = validPhoneNumberFormat('123*');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithDollar', () => {
    const result = validPhoneNumberFormat('123$');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWith!', () => {
    const result = validPhoneNumberFormat('123!');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWith#', () => {
    const result = validPhoneNumberFormat('123#');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWith#', () => {
    const result = validPhoneNumberFormat('123#');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWith%', () => {
    const result = validPhoneNumberFormat('123%');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithinvalidCharacters', () => {
    const result = validPhoneNumberFormat('123$^&*_,:;{}[]');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithValidCharacters', () => {
    const result = validPhoneNumberFormat('+-/()');
    expect(result).toStrictEqual(true);
  });

  test('validPhoneNumberFormat::numberWithSpace', () => {
    const result = validPhoneNumberFormat('456 ');
    expect(result).toStrictEqual(true);
  });

  test('validPhoneNumberFormat::numberWithMultipleSpaces', () => {
    const result = validPhoneNumberFormat('123 456 789');
    expect(result).toStrictEqual(true);
  });

  test('validPhoneNumberFormat::numberWithTabs', () => {
    const result = validPhoneNumberFormat('123\t456\t789');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithNewLines', () => {
    const result = validPhoneNumberFormat('123\n456\n789');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithCarriageReturns', () => {
    const result = validPhoneNumberFormat('123\r456\r789');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithFormFeeds', () => {
    const result = validPhoneNumberFormat('123\f456\f789');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithVerticalTabs', () => {
    const result = validPhoneNumberFormat('123\v456\v789');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });

  test('validPhoneNumberFormat::numberWithfullStop', () => {
    const result = validPhoneNumberFormat('456.');
    expect(result).toBe('Allows numbers and only + - / ( )');
  });
});

describe('core/util/validation/rules::endTimeShouldBeAfterStartTime', () => {
  test('endTimeShouldBeAfterStartTime:: should not validate on empty string', () => {
    let result = endTimeShouldBeAfterStartTime('')('');
    expect(result).toEqual(true);

    result = endTimeShouldBeAfterStartTime('12:00')('');
    expect(result).toEqual(true);
  });

  test('endTimeShouldBeAfterStartTime:: should allow valid time', () => {
    const result = endTimeShouldBeAfterStartTime('08:00')('09:00');
    expect(result).toEqual(true);
  });

  test('endTimeShouldBeAfterStartTime:: should return message on invalid time', () => {
    const result = endTimeShouldBeAfterStartTime('08:00')('07:00');
    expect(result).toEqual('End time should be after start time');
  });

  test('endTimeShouldBeAfterStartTime:: should allow valid time given as function', () => {
    const result = endTimeShouldBeAfterStartTime(() => '08:00')('09:00');
    expect(result).toEqual(true);
  });

  test('endTimeShouldBeAfterStartTime:: should return custom message on invalid time', () => {
    const result = endTimeShouldBeAfterStartTime(
      '08:00',
      'Invalid time',
    )('07:00');
    expect(result).toEqual('Invalid time');
  });

  test('endTimeShouldBeAfterStartTime:: should allow valid time with custom format', () => {
    const result = endTimeShouldBeAfterStartTime('11:00 AM', undefined, {
      timeFormat: 'hh:mm a',
    })('07:00 PM');
    expect(result).toEqual(true);
  });

  test('endTimeShouldBeAfterStartTime:: should allow same time as start time when allowSameTime is true', () => {
    const result = endTimeShouldBeAfterStartTime('11:00', undefined, {
      allowSameTime: true,
    })('11:00');
    expect(result).toEqual(true);
  });

  test('endTimeShouldBeAfterStartTime:: should not allow invalid time when allowSameTime is true', () => {
    const result = endTimeShouldBeAfterStartTime('11:00', undefined, {
      allowSameTime: true,
    })('10:00');
    expect(result).toEqual('End time should be after start time');
  });
});

describe('core/util/validation/rules::startDateShouldBeBeforeEndDate', () => {
  test('startDateShouldBeBeforeEndDate::empty string', () => {
    let result = startDateShouldBeBeforeEndDate('')('');
    expect(result).toBeTruthy();

    result = startDateShouldBeBeforeEndDate('2021-10-25')('');
    expect(result).toBeTruthy();
  });

  test('startDateShouldBeBeforeEndDate::valid', () => {
    const result = startDateShouldBeBeforeEndDate('2021-10-28')('2021-10-25');
    expect(result).toBeTruthy();
  });

  test('startDateShouldBeBeforeEndDate::invalid case', () => {
    const result = startDateShouldBeBeforeEndDate('2021-10-28')('2021-10-31');
    expect(result).toStrictEqual('Start date should be before end date');
  });

  test('startDateShouldBeBeforeEndDate::valid (start date as function)', () => {
    const result = startDateShouldBeBeforeEndDate(() => '2021-10-25')(
      '2021-10-28',
    );
    expect(result).toBeTruthy();
  });

  test('startDateShouldBeBeforeEndDate::invalid case (custom message)', () => {
    const result = startDateShouldBeBeforeEndDate(
      '2021-10-28',
      'From date should be before To date',
    )('2021-10-31');
    expect(result).toStrictEqual('From date should be before To date');
  });

  test('startDateShouldBeBeforeEndDate:: should allow same day as end date when allowSameDate is true', () => {
    const result = startDateShouldBeBeforeEndDate('2021-10-25', undefined, {
      allowSameDate: true,
    })('2021-10-25');
    expect(result).toStrictEqual(true);
  });

  test('startDateShouldBeBeforeEndDate:: should not allow invalid date when allowSameDate is true', () => {
    const result = startDateShouldBeBeforeEndDate('2021-10-25', undefined, {
      allowSameDate: true,
    })('2021-10-31');
    expect(result).toStrictEqual('Start date should be before end date');
  });
});

describe('core/util/validation/rules::startTimeShouldBeBeforeEndTime', () => {
  test('startTimeShouldBeBeforeEndTime:: should not validate on empty string', () => {
    let result = startTimeShouldBeBeforeEndTime('')('');
    expect(result).toEqual(true);

    result = startTimeShouldBeBeforeEndTime('12:00')('');
    expect(result).toEqual(true);
  });

  test('startTimeShouldBeBeforeEndTime:: should allow valid time', () => {
    const result = startTimeShouldBeBeforeEndTime('09:00')('08:00');
    expect(result).toEqual(true);
  });

  test('startTimeShouldBeBeforeEndTime:: should return message on invalid time', () => {
    const result = startTimeShouldBeBeforeEndTime('07:00')('08:00');
    expect(result).toEqual('Start time should be before end time');
  });

  test('startTimeShouldBeBeforeEndTime:: should allow valid time given as function', () => {
    const result = startTimeShouldBeBeforeEndTime(() => '09:00')('08:00');
    expect(result).toEqual(true);
  });

  test('startTimeShouldBeBeforeEndTime:: should return custom message on invalid time', () => {
    const result = startTimeShouldBeBeforeEndTime(
      '07:00',
      'Invalid time',
    )('08:00');
    expect(result).toEqual('Invalid time');
  });

  test('startTimeShouldBeBeforeEndTime:: should allow valid time with custom format', () => {
    const result = startTimeShouldBeBeforeEndTime('07:00 PM', undefined, {
      timeFormat: 'hh:mm a',
    })('11:00 AM');
    expect(result).toEqual(true);
  });

  test('startTimeShouldBeBeforeEndTime:: should allow same time as end time when allowSameTime is true', () => {
    const result = startTimeShouldBeBeforeEndTime('11:00', undefined, {
      allowSameTime: true,
    })('11:00');
    expect(result).toEqual(true);
  });

  test('startTimeShouldBeBeforeEndTime:: should not allow invalid time when allowSameTime is true', () => {
    const result = startTimeShouldBeBeforeEndTime('10:00', undefined, {
      allowSameTime: true,
    })('11:00');
    expect(result).toEqual('Start time should be before end time');
  });
});

describe('core/util/validation/rules::minValueShouldBeLowerThanMaxValue', () => {
  test('minValueShouldBeLowerThanMaxValue:: should not allow minimum value to be greater than maximum value', () => {
    const result = minValueShouldBeLowerThanMaxValue(
      '100',
      'Should be higher than Minimum Salary',
    )('1');
    expect(result).toEqual('Should be higher than Minimum Salary');
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow minimum value to be lower than maximum value', () => {
    const result = minValueShouldBeLowerThanMaxValue(
      '100',
      'Should be higher than Minimum Salary',
    )('101');
    expect(result).toEqual(true);
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow minimum and maximum value as zero', () => {
    const result = minValueShouldBeLowerThanMaxValue('0', undefined)('0');
    expect(result).toEqual(true);
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow minimum and maximum value as empty string literal', () => {
    const result = minValueShouldBeLowerThanMaxValue('', undefined)('');
    expect(result).toEqual(true);
  });
});

describe('core/util/validation/rules::digitsOnly', () => {
  test('digitsOnly:: with empty string', () => {
    const result = digitsOnly('');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnly:: with letters', () => {
    const result = digitsOnly('abcdefg');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnly:: with letters and numbers', () => {
    const result = digitsOnly('123abc');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnly:: with decimal number', () => {
    const result = digitsOnly('10.5');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnly:: with whole number', () => {
    const result = digitsOnly('10');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::digitsOnlyWithDecimalPoint', () => {
  test('digitsOnlyWithDecimalPoint:: with empty string', () => {
    const result = digitsOnlyWithDecimalPoint('');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnlyWithDecimalPoint:: with letters', () => {
    const result = digitsOnlyWithDecimalPoint('abcdefg');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnlyWithDecimalPoint:: with letters and numbers', () => {
    const result = digitsOnlyWithDecimalPoint('123abc');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnlyWithDecimalPoint:: with letters and numbers and decimal point', () => {
    const result = digitsOnlyWithDecimalPoint('123.abc');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnlyWithDecimalPoint:: with decimal number', () => {
    const result = digitsOnlyWithDecimalPoint('10.5');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnlyWithDecimalPoint:: with whole number', () => {
    const result = digitsOnlyWithDecimalPoint('10');
    expect(result).toStrictEqual(true);
  });
});
