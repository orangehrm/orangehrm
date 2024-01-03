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
  <employee-records
    :title-label="$t('maintenance.download_personal_data')"
    include-employees-param="currentAndPast"
    :autocomplete-label="$t('general.employee_name')"
    @search="search"
  ></employee-records>
  <div v-if="employee" class="orangehrm-background-container">
    <selected-employee
      :button-label="$t('general.download')"
      :selected-employee="employee"
      @submit="downloadEmployeeData"
    ></selected-employee>
  </div>
  <br v-if="!employee" />
  <maintenance-note :instance-identifier="instanceIdentifier" />
</template>

<script>
import EmployeeRecords from '@/orangehrmMaintenancePlugin/components/EmployeeRecords';
import SelectedEmployee from '@/orangehrmMaintenancePlugin/components/SelectedEmployee';
import MaintenanceNote from '@/orangehrmMaintenancePlugin/components/MaintenanceNote';

export default {
  components: {
    'employee-records': EmployeeRecords,
    'selected-employee': SelectedEmployee,
    'maintenance-note': MaintenanceNote,
  },

  props: {
    instanceIdentifier: {
      type: String,
      default: null,
    },
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
    downloadEmployeeData(employeeNumber) {
      const downUrl = `${window.appGlobal.baseUrl}/maintenance/accessEmployeeData/${employeeNumber}`;
      window.open(downUrl, '_blank');
    },
  },
};
</script>

<style></style>
