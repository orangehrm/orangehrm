<!--
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
 -->

<template>
  <employee-autocomplete
    :label="$t('admin.assigned_employees')"
    :multiple="true"
    :clear="false"
    :create-options="loadEmployees"
  />
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';

export default {
  name: 'WorkShiftEmployeeAutocomplete',
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/work-shifts/employees',
    );
    return {
      http,
    };
  },
  methods: {
    async loadEmployees(serachParam) {
      return new Promise((resolve) => {
        if (serachParam.trim()) {
          this.http
            .getAll({
              nameOrId: serachParam.trim(),
            })
            .then(({data}) => {
              resolve(
                data.data.map((employee) => {
                  return {
                    id: employee.empNumber,
                    label: `${employee.firstName} ${employee.middleName} ${employee.lastName}`,
                    isPastEmployee: employee.terminationId ? true : false,
                  };
                }),
              );
            });
        } else {
          resolve([]);
        }
      });
    },
  },
};
</script>
