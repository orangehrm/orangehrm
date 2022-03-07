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
  <employee-records
    @search="search"
    :titleLabel="'Download Personal Data'"
    :includeEmployeesParam="'currentAndPast'"
  ></employee-records>
  <div v-if="employee" class="orangehrm-background-container">
    <selected-employee
      :buttonLabel="'Download'"
      :selectedEmployee="getUpdateEmployee"
      :disableField="true"
      @submit="getEmployeeNumber"
    ></selected-employee>
  </div>
</template>

<script>
import EmployeeRecords from '@/orangehrmMaintenancePlugin/components/EmployeeRecords';
import SelectedEmployee from '@/orangehrmMaintenancePlugin/components/SelectedEmployee';
import {APIService} from '@/core/util/services/api.service';

export default {
  components: {
    'employee-records': EmployeeRecords,
    'selected-employee': SelectedEmployee,
  },
  data() {
    return {
      employee: null,
    };
  },
  methods: {
    search(employee) {
      this.employee = employee;
    },
    getEmployeeNumber(employeeNumber) {
      const downUrl = `${window.appGlobal.baseUrl}/maintenance/accessEmployeeData/${employeeNumber}`;
      window.open(downUrl, '_blank');
    },
  },

  computed: {
    getUpdateEmployee: {
      get() {
        return this.employee;
      },
    },
  },
};
</script>

<style></style>
