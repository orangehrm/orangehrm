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
  required,
  afterDate,
  endDateShouldBeAfterStartDate,
  validEmailFormat,
  validPhoneNumberFormat,
  endTimeShouldBeAfterStartTime,
  startDateShouldBeBeforeEndDate,
  startTimeShouldBeBeforeEndTime,
  minValueShouldBeLowerThanMaxValue,
  maxValueShouldBeGreaterThanMinValue,
  numberShouldBeBetweenMinAndMaxValue,
  digitsOnly,
  digitsOnlyWithDecimalPoint,
  digitsOnlyWithDecimalPointAndMinusSign,
  shouldBeCurrentOrPreviousDate,
  validHexFormat,
  imageShouldHaveDimensions,
  greaterThanOrEqual,
  lessThanOrEqual,
  File,
  validLangString,
  validSelection,
  validHostnameFormat,
  validPortRange,
  validVideoURL,
  digitsOnlyWithTwoDecimalPoints,
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
      'general.should_be_a_number_between_min_and_max':
        'Should be a number between 0-100',
      'general.invalid': 'Invalid',
      'general.incorrect_dimensions': 'Incorrect Dimensions',
      'general.less_than_or_equal_to_n':
        'Number should be less than or equal to 100',
      'general.greater_than_or_equal_to_n':
        'Number should be greater than or equal to 0',
      'general.enter_valid_port_between_a_to_b':
        'Enter a valid port number between 0 to 65535',
      'general.invalid_video_url_message':
        'This URL is not a valid URL of a video or it is not supported by the system',
      'claim.should_be_a_valid_number': 'Should be a valid number (xxx.xx)',
    };
    return mockStrings[langString];
  };
  return {translate: jest.fn(() => t)};
});

const createMockFile = (
  type = 'image/gif',
  base64 = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
  name = 'mock',
  size = 100,
): File => {
  return {
    name,
    type,
    size,
    base64,
  };
};

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
    // @ts-expect-error: forcing unsupported type for testing
    let result = required({test: 'Object'});
    expect(result).toBeTruthy();

    // @ts-expect-error: forcing unsupported type for testing
    result = required(null);
    expect(result).toBe('Required');
  });

  test('required::unsupported type', () => {
    // @ts-expect-error: forcing unsupported type for testing
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

  test('validEmailFormat:invalid character at first', () => {
    const result = validEmailFormat('>test@deviohrm.com');
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

  test('validEmailFormat:validEmail main domain', () => {
    const result = validEmailFormat('devi@ohrm.com');
    expect(result).toStrictEqual(true);
  });

  test('validEmailFormat:validEmail sub domain', () => {
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

describe('core/util/validation/rules::maxValueShouldBeGreaterThanMinValue', () => {
  test('maxValueShouldBeGreaterThanMinValue:: should not allow minimum value to be greater than maximum value', () => {
    const result = maxValueShouldBeGreaterThanMinValue(
      '100',
      'Should be higher than Minimum Salary',
    )('1');
    expect(result).toEqual('Should be higher than Minimum Salary');
  });

  test('maxValueShouldBeGreaterThanMinValue:: should allow minimum value to be lower than maximum value', () => {
    const result = maxValueShouldBeGreaterThanMinValue(
      '100',
      'Should be higher than Minimum Salary',
    )('101');
    expect(result).toEqual(true);
  });

  test('maxValueShouldBeGreaterThanMinValue:: should allow minimum and maximum value as zero', () => {
    const result = maxValueShouldBeGreaterThanMinValue('0', undefined)('0');
    expect(result).toEqual(true);
  });

  test('maxValueShouldBeGreaterThanMinValue:: should allow minimum and maximum value as empty string literal', () => {
    const result = maxValueShouldBeGreaterThanMinValue('', undefined)('');
    expect(result).toEqual(true);
  });

  test('maxValueShouldBeGreaterThanMinValue:: should not allow when only value is empty string', () => {
    const result = maxValueShouldBeGreaterThanMinValue(
      '10000',
      'Should be higher than Minimum Salary',
    )('');
    expect(result).toEqual('Should be higher than Minimum Salary');
  });

  test('maxValueShouldBeGreaterThanMinValue:: should not allow when the minimum is given and the value is 0', () => {
    const result = maxValueShouldBeGreaterThanMinValue(
      '10000',
      'Should be higher than Minimum Salary',
    )('0');
    expect(result).toEqual('Should be higher than Minimum Salary');
  });
});

describe('core/util/validation/rules::minValueShouldBeLowerThanMaxValue', () => {
  test('minValueShouldBeLowerThanMaxValue:: should not allow minimum value to be greater than maximum value', () => {
    const result = minValueShouldBeLowerThanMaxValue(
      '10',
      'Should be lower than Maximum Rating',
    )('100');
    expect(result).toEqual('Should be lower than Maximum Rating');
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow minimum value to be lower than maximum value', () => {
    const result = minValueShouldBeLowerThanMaxValue(
      '100',
      'Should be lower than Maximum Rating',
    )('10');
    expect(result).toEqual(true);
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow minimum and maximum value to be zero', () => {
    const result = minValueShouldBeLowerThanMaxValue('0', undefined)('0');
    expect(result).toEqual(true);
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow minimum and maximum value to be an empty string literal', () => {
    const result = minValueShouldBeLowerThanMaxValue('', undefined)('');
    expect(result).toEqual(true);
  });

  test('minValueShouldBeLowerThanMaxValue:: should allow if only value is empty string', () => {
    const result = minValueShouldBeLowerThanMaxValue(
      '10000',
      'Should be lower than Maximum Salary',
    )('');
    expect(result).toEqual(true);
  });
});

describe('core/util/validation/rules::numberShouldBeBetweenMinAndMaxValue', () => {
  test('numberShouldBeBetweenMinAndMaxValue:: should not allow number to be out of the given range', () => {
    const result = numberShouldBeBetweenMinAndMaxValue(0, 100)('101');
    expect(result).toEqual('Should be a number between 0-100');
  });

  test('numberShouldBeBetweenMinAndMaxValue:: should allow number to be between 0 and 100', () => {
    const result = numberShouldBeBetweenMinAndMaxValue(0, 100)('10');
    expect(result).toEqual(true);
  });

  test('numberShouldBeBetweenMinAndMaxValue:: should display custom message', () => {
    const result = numberShouldBeBetweenMinAndMaxValue(
      0,
      10,
      'Rating should be between 0 and 10',
    )('11');
    expect(result).toEqual('Rating should be between 0 and 10');
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

describe('core/util/validation/rules::digitsOnlyWithDecimalPointAndMinusSign', () => {
  test('digitsOnlyWithDecimalPointAndMinusSign:: with empty string', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with letters', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('abcdefg');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with letters and numbers', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('123abc');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with letters and numbers and decimal point', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('123.abc');
    expect(result).toEqual('Should be a number');
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with decimal number', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('10.5');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with whole number', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('10');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with negative whole number', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('-10');
    expect(result).toStrictEqual(true);
  });

  test('digitsOnlyWithDecimalPointAndMinusSign:: with negative decimal number', () => {
    const result = digitsOnlyWithDecimalPointAndMinusSign('-10.231');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::shouldBeCurrentOrPreviousDate', () => {
  test('shouldBeCurrentOrPreviousDate::empty string', () => {
    let result = shouldBeCurrentOrPreviousDate()('');
    expect(result).toBeTruthy();

    result = shouldBeCurrentOrPreviousDate()('');
    expect(result).toBeTruthy();
  });

  test('shouldBeCurrentOrPreviousDate::valid', () => {
    const result = shouldBeCurrentOrPreviousDate()('2022-01-29');
    expect(result).toBeTruthy();
  });
});

describe('core/util/validation/rules::validHexFormat', () => {
  it('validHexFormat:: valid 6 digit Hex', () => {
    const result = validHexFormat('#ff0000');
    expect(result).toStrictEqual(true);
  });
  it('validHexFormat:: valid 3 digit Hex', () => {
    const result = validHexFormat('#f00');
    expect(result).toStrictEqual(true);
  });
  it('validHexFormat:: invalid 6 digit Hex', () => {
    const result = validHexFormat('#ffx000');
    expect(result).toStrictEqual('Invalid');
  });
  it('validHexFormat:: invalid 3 digit Hex', () => {
    const result = validHexFormat('#ffx');
    expect(result).toStrictEqual('Invalid');
  });
  it('validHexFormat:: non Hex string', () => {
    const result = validHexFormat('car');
    expect(result).toStrictEqual('Invalid');
  });
});

describe('core/util/validation/rules::imageShouldHaveDimensions', () => {
  // Mock Image class methods since jsdom is not supported
  const mockDomImage = (width: number, height: number) => {
    Image.prototype.decode = function () {
      this.width = width;
      this.height = height;
      return Promise.resolve();
    };
  };

  test('imageShouldHaveDimensions:: Invalid file dimensions', async () => {
    mockDomImage(100, 75);
    const result = await imageShouldHaveDimensions(50 / 50)(createMockFile());
    expect(result).toStrictEqual('Incorrect Dimensions');
  });

  test('imageShouldHaveDimensions:: Valid file dimensions', async () => {
    mockDomImage(50, 50);
    const result = await imageShouldHaveDimensions(50 / 50)(createMockFile());
    expect(result).toStrictEqual(true);
  });

  test('imageShouldHaveDimensions:: should allow null files', async () => {
    const result = await imageShouldHaveDimensions(50 / 50)(null);
    expect(result).toStrictEqual(true);
  });

  test('imageShouldHaveDimensions:: ignore SVG size validation', async () => {
    const result = await imageShouldHaveDimensions(50 / 50)(
      createMockFile('image/svg+xml', '', 'mock.svg'),
    );
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::lessThanOrEqual', () => {
  test('lessThanOrEqual:: with empty string', () => {
    const result = lessThanOrEqual(100)('');
    expect(result).toStrictEqual(true);
  });

  test('lessThanOrEqual:: with string', () => {
    const result = lessThanOrEqual(100)('number');
    expect(result).toEqual('Number should be less than or equal to 100');
  });

  test('lessThanOrEqual:: with invalid number', () => {
    const result = lessThanOrEqual(100)('100.asdas232');
    expect(result).toEqual('Number should be less than or equal to 100');
  });

  test('lessThanOrEqual:: with number higher than max', () => {
    const result = lessThanOrEqual(100)('1000');
    expect(result).toEqual('Number should be less than or equal to 100');
  });

  test('lessThanOrEqual:: with decimal number higher than max', () => {
    const result = lessThanOrEqual(100)('100.1');
    expect(result).toEqual('Number should be less than or equal to 100');
  });

  test('lessThanOrEqual:: with number lower than max', () => {
    const result = lessThanOrEqual(100)('99');
    expect(result).toStrictEqual(true);
  });

  test('lessThanOrEqual:: with decimal number lower than max', () => {
    const result = lessThanOrEqual(100)('99.9');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::greaterThanOrEqual', () => {
  test('greaterThanOrEqual:: with empty string', () => {
    const result = greaterThanOrEqual(0)('');
    expect(result).toStrictEqual(true);
  });

  test('greaterThanOrEqual:: with string', () => {
    const result = greaterThanOrEqual(0)('number');
    expect(result).toEqual('Number should be greater than or equal to 0');
  });

  test('greaterThanOrEqual:: with invalid number', () => {
    const result = greaterThanOrEqual(0)('100.asdas232');
    expect(result).toEqual('Number should be greater than or equal to 0');
  });

  test('greaterThanOrEqual:: with number lower than min', () => {
    const result = greaterThanOrEqual(0)('-100');
    expect(result).toEqual('Number should be greater than or equal to 0');
  });

  test('greaterThanOrEqual:: with decimal number lower than max', () => {
    const result = greaterThanOrEqual(0)('-0.1');
    expect(result).toEqual('Number should be greater than or equal to 0');
  });

  test('greaterThanOrEqual:: with number higher than min', () => {
    const result = greaterThanOrEqual(0)('1');
    expect(result).toStrictEqual(true);
  });

  test('greaterThanOrEqual:: with decimal number higher than min', () => {
    const result = greaterThanOrEqual(0)('0.1');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::validLangString', () => {
  test('validLangString:: without close brace', () => {
    const result = validLangString('{abcd');
    expect(result).toStrictEqual('Invalid');
  });

  test('validLangString:: with close brace', () => {
    const result = validLangString('{abcd}');
    expect(result).toStrictEqual(true);
  });

  test('validLangString:: multiple strings with close brace', () => {
    const result = validLangString('{abcd} {pqrs}');
    expect(result).toStrictEqual(true);
  });

  test('validLangString:: without close brace - multiple string', () => {
    const result = validLangString('{abcd} {pqrs');
    expect(result).toStrictEqual('Invalid');
  });

  test('validLangString:: without braces', () => {
    const result = validLangString('abcd pqrs');
    expect(result).toStrictEqual(true);
  });

  test('validLangString:: multiple strings with close brace', () => {
    const result = validLangString('abcd {pqrs}');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::validSelection', () => {
  test('validSelection:: with property selected', () => {
    const result = validSelection({id: 1, label: 'System User'});
    expect(result).toStrictEqual(true);
  });

  test('validSelection:: with string value', () => {
    const result = validSelection('system user');
    expect(result).toStrictEqual('Invalid');
  });

  test('validSelection:: with null value', () => {
    const result = validSelection(null);
    expect(result).toStrictEqual(true);
  });

  test('validSelection:: with empty array value', () => {
    const result = validSelection([]);
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::validHostnameFormat', () => {
  test('validHostnameFormat:: without top level domain', () => {
    const result = validHostnameFormat('localhost');
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: with top level domain', () => {
    const result = validHostnameFormat('orangehrm.com');
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: with sub domain', () => {
    const result = validHostnameFormat('osohrm.orangehrm.com');
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: valid local ip', () => {
    const result = validHostnameFormat('127.0.0.1');
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: valid ipv4 ip', () => {
    const result = validHostnameFormat('8.8.8.8');
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: hostname with invalid characters', () => {
    const result = validHostnameFormat('orangehrm_company.com');
    expect(result).toStrictEqual('Invalid');
  });

  test('validHostnameFormat:: hostname with space characters', () => {
    const result = validHostnameFormat('orangehrm com');
    expect(result).toStrictEqual('Invalid');
  });

  test('validHostnameFormat:: hostname with protocol', () => {
    const result = validHostnameFormat('http://orangehrm.com');
    expect(result).toStrictEqual('Invalid');
  });

  test('validHostnameFormat:: hostname with invalid ip', () => {
    const result = validHostnameFormat('555.555.555.555');
    expect(result).toStrictEqual('Invalid');
  });

  test('validHostnameFormat:: hostname with incomplete ip', () => {
    const result = validHostnameFormat('1.1.1');
    expect(result).toStrictEqual('Invalid');
  });

  test('validHostnameFormat:: with null value', () => {
    const result = validSelection(null);
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: hostname with unicode characters', () => {
    const result = validHostnameFormat('localhost.世界');
    expect(result).toStrictEqual(true);
  });

  test('validHostnameFormat:: hostname with punycode', () => {
    const result = validHostnameFormat('xn--ggle-0nda.com');
    expect(result).toStrictEqual(true);
  });
});

describe('core/util/validation/rules::validPortRange', () => {
  const _validPortRange = validPortRange(5, 0, 65535);

  test('validPortRange:: with port value in range', () => {
    const result = _validPortRange('389');
    expect(result).toStrictEqual(true);
  });

  test('validPortRange:: with port value out of range', () => {
    const result = _validPortRange('150000');
    expect(result).toStrictEqual(
      'Enter a valid port number between 0 to 65535',
    );
  });

  test('validPortRange:: with invalid port value', () => {
    const result = _validPortRange('port9');
    expect(result).toStrictEqual(
      'Enter a valid port number between 0 to 65535',
    );
  });

  test('validPortRange:: with negative value', () => {
    const result = _validPortRange('-333');
    expect(result).toStrictEqual(
      'Enter a valid port number between 0 to 65535',
    );
  });
});

describe('core/util/validation/rules::validVideoURL', () => {
  test('validVideoURL:valid full youtube URL', () => {
    const result = validVideoURL('https://www.youtube.com/watch?v=4dDP_1lGbYs');
    expect(result).toStrictEqual(true);
  });

  test('validVideoURL:valid shortened youtube URL', () => {
    const result = validVideoURL('https://youtu.be/4dDP_1lGbYs');
    expect(result).toStrictEqual(true);
  });

  test('validVideoURL:valid mobile youtube URL', () => {
    const result = validVideoURL('https://m.youtube.com/watch?v=4dDP_1lGbYs');
    expect(result).toStrictEqual(true);
  });

  test('validVideoURL:valid embed youtube URL', () => {
    const result = validVideoURL('https://www.youtube.com/embed/4dDP_1lGbYs');
    expect(result).toStrictEqual(true);
  });

  test('validVideoURL:valid youtube short URL', () => {
    const result = validVideoURL('https://www.youtube.com/shorts/dCsmH5BfpdQ');
    expect(result).toStrictEqual(true);
  });

  test('validVideoURL:valid youtube URL without protocol', () => {
    const result = validVideoURL('www.youtube.com/watch?v=4dDP_1lGbYs');
    expect(result).toStrictEqual(true);
  });

  test('validVideoURL:not a url', () => {
    const result = validVideoURL('abcd');
    expect(result).toBe(
      'This URL is not a valid URL of a video or it is not supported by the system',
    );
  });

  test('validVideoURL:invalid url', () => {
    const result = validVideoURL('https://www.youtube.com');
    expect(result).toBe(
      'This URL is not a valid URL of a video or it is not supported by the system',
    );
  });
});

describe('core/util/validation/rules::digitsOnlyWithTwoDecimalPoints', () => {
  test('digitsOnlyWithTwoDecimalPoints:: with empty string', () => {
    const result = digitsOnlyWithTwoDecimalPoints('');
    expect(result).toEqual(true);
  });

  test('digitsOnlyWithTwoDecimalPoints:: with two decimal points', () => {
    const result = digitsOnlyWithTwoDecimalPoints('123.98');
    expect(result).toEqual(true);
  });

  test('digitsOnlyWithTwoDecimalPoints:: with one decimal points', () => {
    const result = digitsOnlyWithTwoDecimalPoints('123.9');
    expect(result).toEqual(true);
  });

  test('digitsOnlyWithTwoDecimalPoints:: with three decimal points', () => {
    const result = digitsOnlyWithTwoDecimalPoints('123.998');
    expect(result).toEqual('Should be a valid number (xxx.xx)');
  });

  test('digitsOnlyWithTwoDecimalPoints:: with no decimal points', () => {
    const result = digitsOnlyWithTwoDecimalPoints('123.');
    expect(result).toEqual('Should be a valid number (xxx.xx)');
  });
});
