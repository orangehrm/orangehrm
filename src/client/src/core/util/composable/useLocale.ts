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

import usei18n from './usei18n';
import {buildLocale} from '@ohrm/oxd';

export default function useLocale() {
  const {$t} = usei18n();

  const locale: Locale = buildLocale({
    months: {
      wide: [
        $t('general.january'),
        $t('general.february'),
        $t('general.march'),
        $t('general.april'),
        $t('general.may'),
        $t('general.june'),
        $t('general.july'),
        $t('general.august'),
        $t('general.september'),
        $t('general.october'),
        $t('general.november'),
        $t('general.december'),
      ],
      abbreviated: [
        $t('general.jan'),
        $t('general.feb'),
        $t('general.mar'),
        $t('general.apr'),
        $t('general.may'),
        $t('general.jun'),
        $t('general.jul'),
        $t('general.aug'),
        $t('general.sep'),
        $t('general.oct'),
        $t('general.nov'),
        $t('general.dec'),
      ],
    },
    days: {
      abbreviated: [
        $t('general.sun'),
        $t('general.mon'),
        $t('general.tue'),
        $t('general.wed'),
        $t('general.thu'),
        $t('general.fri'),
        $t('general.sat'),
      ],
      wide: [
        $t('general.sunday'),
        $t('general.monday'),
        $t('general.tuesday'),
        $t('general.wednesday'),
        $t('general.thursday'),
        $t('general.friday'),
        $t('general.saturday'),
      ],
    },
  });

  return {
    locale,
  };
}
