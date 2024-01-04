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

import {computed, ref, watch} from 'vue';

type Order = 'ASC' | 'DESC' | 'DEFAULT';

interface SortDefinition {
  [column: string]: Order;
}

interface SortParams {
  sortDefinition: SortDefinition;
}

export default function useSort(sortParams: SortParams) {
  const sortDefinition = ref({
    ...JSON.parse(JSON.stringify(sortParams.sortDefinition)),
  });

  const sortField = computed(() => {
    return Object.keys(sortDefinition.value).filter((column) => {
      const order = sortDefinition.value[column];
      return order && order != 'DEFAULT';
    })[0];
  });

  const sortOrder = computed(() => {
    return sortDefinition.value[sortField.value];
  });

  const onSort = (func: () => void) => watch(sortDefinition, func);

  return {
    sortDefinition,
    sortField,
    sortOrder,
    onSort,
  };
}
