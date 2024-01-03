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

import {truncate} from '../truncate';

describe('core/util/helper/truncate', () => {
  const sampleText =
    'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Autem cumque, ipsa minima ducimus laboriosam accusamus corporis. Pariatur corporis facilis iure mollitia quaerat dolorem ipsam provident quo nostrum, similique numquam consectetur?';
  const shortText = 'Lorem ipsum dolor';
  const unicodeSampleText =
    '국민경제의 발전을 위한 중요정책의 수립에 관하여 대통령의 자문에 응하기 위하여 국민경제자문회의를 둘 수 있다';
  const unicodeShortText =
    '국민경제의 발전을 위한 중요정책의 수립에 관하여 대통령의 자문에 응하기 위하여 국민경제자문...';
  const unicodeSampleText2 =
    'ලෝරීම් ඉප්සම් යනු සරලව මුද්‍රණ හා අකුරු ඇමිනුම් කර්මාන්තයේ උදාහරණ අකුරු පෙළ වෙයි. එය ශතවර්ශ පහක් පමණ නොවී පැමිණ ඉලෙක්ට්‍රොනික යුගයටද පිවිසුණි';

  test('truncate::with default param should output text truncated to 50 chars + 3 chars ellipsis', () => {
    const result = truncate(sampleText);
    expect(result.length).toStrictEqual(53);
  });

  test('truncate::with custom length should output matching length + 3 chars ellipsis', () => {
    const result = truncate(sampleText, {length: 20});
    expect(result.length).toStrictEqual(23);
  });

  test('truncate::with default param should output empty text when undefined', () => {
    const result = truncate(undefined);
    expect(result).toStrictEqual('');
  });

  test('truncate::with text shorter than truncate length should not be affected', () => {
    const result = truncate(shortText);
    expect(result).toStrictEqual(shortText);
  });

  test('truncate::with text shorter than truncate length should not be affected', () => {
    const result = truncate(shortText);
    expect(result).toStrictEqual(shortText);
  });

  test('truncate::with default param should output unicode text truncated', () => {
    const result = truncate(unicodeSampleText);
    expect(result).toStrictEqual(unicodeShortText);
  });

  test('truncate::with length 20 should output unicode text with length 23', () => {
    const result = truncate(unicodeSampleText2, {length: 20});
    expect(result.length).toStrictEqual(23);
  });
});
