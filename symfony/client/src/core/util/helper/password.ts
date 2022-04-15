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
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';

const translate = translatorFactory();

export function getPassLevel(password: string): number[] {
  const level1 = new RegExp(/[a-z]/);
  const level2 = new RegExp(/[A-Z]/);
  const level3 = new RegExp(/[0-9]/);
  const level4 = new RegExp(/[@#\\/\-!$%^&*()_+|~=`{}[\]:";'<>?,.]/);
  return [level1, level2, level3, level4].map(level => {
    return level.test(password) ? 1 : 0;
  });
}

export function checkPassword(password: string): string | boolean {
  if (password.length >= 8) {
    const pwdLevel = getPassLevel(password);
    if (RegExp(/\s/).test(password)) {
      return translate('auth.your_password_should_not_contain_spaces');
    }
    if (pwdLevel.reduce((acc, curr) => acc + curr, 0) < 4) {
      return translate(
        'auth.must_contain_lower_case_upper_case_digit_character_message',
      );
    } else {
      return true;
    }
  } else {
    return translate('auth.should_have_min_8_characters');
  }
}
