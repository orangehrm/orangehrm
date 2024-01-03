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

import usei18n from '@/core/util/composable/usei18n';

type Employee = {
  firstName: string;
  middleName: string | null;
  lastName: string;
  terminationId: number | null;
};

type Options = {
  includeMiddle?: boolean;
  excludePastEmpTag?: boolean;
};

export default function useEmployeeNameTranslate() {
  const {$t} = usei18n();

  const translateEmployeeName = (
    employee: Employee,
    options?: Options,
  ): string => {
    if (employee.firstName === 'Purged' && employee.lastName === 'Employee') {
      return $t('general.purged_employee');
    }

    const includeMiddle = options?.includeMiddle;
    const excludePastEmpTag = options?.excludePastEmpTag;

    const resolvedMiddleName =
      typeof includeMiddle === 'boolean' &&
      includeMiddle &&
      typeof employee.middleName === 'string'
        ? ` ${employee.middleName} `
        : ' ';

    if (employee.terminationId) {
      const resolvedPastEmpTag =
        typeof excludePastEmpTag === 'undefined'
          ? ` ${$t('general.past_employee')}`
          : excludePastEmpTag
          ? ''
          : ` ${$t('general.past_employee')}`;

      return `${employee.firstName}${resolvedMiddleName}${employee.lastName}${resolvedPastEmpTag}`;
    }

    return `${employee.firstName}${resolvedMiddleName}${employee.lastName}`;
  };

  return {
    $tEmpName: translateEmployeeName,
  };
}
