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
  <div class="orangehrm-background-container">
    <verify-password
      v-if="!verified"
      :title-label="title"
      @verify="onClickVerify"
    />
    <purge-employee-records
      v-if="verified"
      include-employees-param="onlyPast"
      :title-label="title"
      autocomplete-label="Past Employee"
      @search="onClickSearch"
    />
    <br />
    <selected-employee
      v-if="showPurgeableEmployee"
      :loading="isLoading"
      :selected-employee="selectedEmployee"
      button-label="Purge"
      @submit="onClickPurge"
    />

    <purge-confirmation
      ref="purgeDialog"
      title="Purge Employee"
      subtitle="You are about to purge the employee permanently. Are you sure you want to continue? This operation cannot be undone"
      cancel-label="No, Cancel"
      confirmation-label="Yes, Purge"
      icon=""
    ></purge-confirmation>
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import SelectedEmployee from '@/orangehrmMaintenancePlugin/components/SelectedEmployee';
import EmployeeRecords from '@/orangehrmMaintenancePlugin/components/EmployeeRecords';
import VerifyPassword from '@/orangehrmMaintenancePlugin/components/VerifyPassword';
import ConfirmationDialog from '@/core/components/dialogs/ConfirmationDialog';

const selectedEmployeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  employeeId: '',
  empNumber: '',
};

export default {
  name: 'PurgeEmployee',
  components: {
    'purge-confirmation': ConfirmationDialog,
    'verify-password': VerifyPassword,
    'purge-employee-records': EmployeeRecords,
    'selected-employee': SelectedEmployee,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/maintenance/purge',
    );

    return {
      http,
    };
  },

  data() {
    return {
      title: 'Purge Employee Records',
      //TODO change after implementing password verification
      verified: true,
      isLoading: false,
      showPurgeableEmployee: false,
      selectedEmployee: {...selectedEmployeeModel},
    };
  },

  methods: {
    onClickVerify() {
      this.verified = true;
    },
    onClickSearch(employee) {
      this.selectedEmployee = {...selectedEmployeeModel};
      if (employee) {
        this.selectedEmployee = {...employee};
        this.showPurgeableEmployee = true;
      } else {
        this.showPurgeableEmployee = false;
      }
    },
    onClickPurge(empNumber) {
      this.$refs.purgeDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.purgeEmployee(empNumber);
        }
      });
    },
    purgeEmployee(empNumber) {
      this.isLoading = true;
      this.http
        .deleteAll({
          empNumber: empNumber,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Purged',
          });
        })
        .then(() => {
          this.showPurgeableEmployee = false;
          this.selectedEmployee = {...selectedEmployeeModel};
          this.isLoading = false;
          navigate('/maintenance/purgeEmployee');
        });
    },
  },
};
</script>
