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
    :label="$t('performance.reviewers')"
    :multiple="true"
    :clear="false"
    :create-options="loadEmployees"
    required
  />
</template>

<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'ReviewersAutoComplete',
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },
  props: {
    includeEmployees: {
      type: String,
      default: 'onlyCurrent',
    },
    excludeEmployee: {
      type: Object,
      required: false,
      default: null,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/trackers/reviewers',
    );
    return {
      http,
    };
  },
  methods: {
    async loadEmployees(searchParam) {
      return new Promise((resolve) => {
        if (searchParam.trim()) {
          this.http
            .getAll({
              nameOrId: searchParam.trim(),
              empNumber:
                this.excludeEmployee == null ? null : this.excludeEmployee.id, //to be added back when a seperate API is created for reviewers
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
