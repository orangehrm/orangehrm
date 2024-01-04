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
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-icon type="svg" name="leaveAlt" />
      <div class="orangehrm-config-title">
        <oxd-text type="card-body">
          {{ $t('dashboard.employees_on_leave_today') }}
        </oxd-text>
        <oxd-text type="card-title">
          {{ $t('dashboard.configurations') }}
        </oxd-text>
      </div>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row class="orangehrm-config-checkbox">
        <oxd-input-field
          v-model="showAccessibleEmployeesOnly"
          type="switch"
          :label="
            $t(
              'dashboard.only_show_accessible_employees_on_leave_for_other_users',
            )
          "
        />
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions>
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {OxdDialog, OxdIcon} from '@ohrm/oxd';

export default {
  name: 'EmployeesOnLeaveConfigModal',
  components: {
    'oxd-icon': OxdIcon,
    'oxd-dialog': OxdDialog,
  },
  emits: ['close'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/dashboard/config/employee-on-leave-today',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      showAccessibleEmployeesOnly: false,
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        this.showAccessibleEmployeesOnly =
          data.showOnlyAccessibleEmployeesOnLeaveToday;
      })
      .finally(() => (this.isLoading = false));
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            showOnlyAccessibleEmployeesOnLeaveToday:
              this.showAccessibleEmployeesOnly,
          },
        })
        .then(() => {
          this.$toast.updateSuccess();
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-modal-header {
  display: flex;
  align-items: center;
}
.orangehrm-config-title {
  margin-left: 1rem;
}
.orangehrm-config-checkbox {
  & .oxd-input-group {
    margin: 0;
    padding: 1rem 0;
    flex-direction: row;
  }
  ::v-deep(.oxd-input-group__label-wrapper) {
    margin: 0;
    margin-right: 1rem;
  }
}
</style>
