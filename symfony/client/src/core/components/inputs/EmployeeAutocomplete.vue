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
  <oxd-input-field
    type="autocomplete"
    label="Employee Name"
    :clear="false"
    :create-options="loadEmployees"
  >
    <template v-slot:afterSelected="{data}">
      <template v-if="data.isPastEmployee">(Past Employee)</template>
    </template>
    <template v-slot:option="{data}">
      <span>{{ data.label }}</span>
      <div v-if="data.isPastEmployee" class="past-employee-tag">
        (Past Employee)
      </div>
    </template>
  </oxd-input-field>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
export default {
  name: 'employee-autocomplete',
  props: {
    params: {
      type: Object,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
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
              nameOrId: serachParam,
              ...this.params,
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
