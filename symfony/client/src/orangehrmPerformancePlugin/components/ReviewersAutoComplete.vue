<!--
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
 -->

<template>
  <employee-autocomplete
    label="Reviewers"
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
      type:String,
      default: 'onlyCurrent',
    },
    excludeEmployee: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
        window.appGlobal.baseUrl,
        'api/v2/performance/reviewers',
        //'/api/v2/pim/employees',
    );
    return {
      http,
    };
  },
  methods: {
    async loadEmployees(serachParam) {
      return new Promise(resolve => {
        if (serachParam.trim()) {
          this.http
            .getAll({
              nameOrId: serachParam.trim(),
              //includeEmployees: this.includeEmployees,
              empNumber: this.excludeEmployee.id,   //to be added back when a seperate API is created for reviewers
            })
            .then(({data}) => {
              resolve(
                data.data.map(employee => {
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
<style scoped>
.past-employee-tag {
  margin-left: auto;
}
</style>
