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
  <teleport to="#app">
    <oxd-dialog
      v-if="show"
      :style="{maxWidth: '450px'}"
      @update:show="onCancel"
    >
      <div class="orangehrm-modal-header">
        <oxd-text type="card-title">
          {{ $t('leave.updating_entitlement') }}
        </oxd-text>
      </div>
      <div class="orangehrm-text-center-align">
        <oxd-text type="card-body">
          {{
            $t('leave.entitlement_value_confirmation_message', {
              oldvalue: current,
              newvalue: updateAs,
            })
          }}
        </oxd-text>
      </div>
      <div class="orangehrm-modal-footer">
        <oxd-button
          display-type="ghost"
          class="orangehrm-button-margin"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <oxd-button
          display-type="secondary"
          class="orangehrm-button-margin"
          :label="$t('general.confirm')"
          @click="onConfirm"
        />
      </div>
    </oxd-dialog>
  </teleport>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {OxdDialog} from '@ohrm/oxd';

export default {
  name: 'EntitlementUpdateModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '');
    return {
      http,
    };
  },
  data() {
    return {
      show: false,
      reject: null,
      resolve: null,
      current: '0.00',
      updateAs: '0.00',
    };
  },
  methods: {
    showDialog() {
      return this.http
        .request({
          method: 'GET',
          url: `/api/v2/leave/employees/${this.data.employee?.id}/leave-entitlements`,
          params: {
            leaveTypeId: this.data.leaveType?.id,
            fromDate: this.data.leavePeriod?.startDate,
            toDate: this.data.leavePeriod?.endDate,
            entitlement: this.data.entitlement,
          },
        })
        .then((response) => {
          const {data} = response.data;
          this.current = data.entitlement?.current
            ? parseFloat(data.entitlement.current).toFixed(2)
            : '0.00';
          this.updateAs = data.entitlement?.updateAs
            ? parseFloat(data.entitlement.updateAs).toFixed(2)
            : '0.00';
          return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
            this.show = true;
          });
        });
    },
    onConfirm() {
      this.show = false;
      this.resolve && this.resolve('ok');
    },
    onCancel() {
      this.show = false;
      this.resolve && this.resolve('cancel');
    },
  },
};
</script>

<style scoped>
.orangehrm-modal-header {
  margin-bottom: 1.2rem;
  display: flex;
  justify-content: center;
}
.orangehrm-modal-footer {
  margin-top: 1.2rem;
  display: flex;
  justify-content: center;
}
.orangehrm-button-margin {
  margin: 0.25rem;
}
.orangehrm-text-center-align {
  text-align: center;
}
</style>
