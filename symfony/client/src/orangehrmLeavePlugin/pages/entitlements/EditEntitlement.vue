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
    <div class="orangehrm-card-container">
      <oxd-text class="orangehrm-main-title">
        {{ $t('leave.edit_leave_entitlement') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="leaveEntitlement.employee"
                disabled
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <leave-type-dropdown
                v-model="leaveEntitlement.leaveType"
                required
                disabled
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('leave.leave_period')"
                :options="leavePeriods"
                :rules="rules.leavePeriod"
                v-model="leaveEntitlement.leavePeriod"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :rules="rules.entitlement"
                :label="$t('leave.entitlement')"
                v-model="leaveEntitlement.entitlement"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            displayType="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import promiseDebounce from '@orangehrm/oxd/utils/promiseDebounce';

const leaveEntitlementModel = {
  employee: null,
  leaveType: null,
  leavePeriod: null,
  entitlement: '',
};

export default {
  components: {
    'leave-type-dropdown': LeaveTypeDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
  },

  props: {
    entitlementId: {
      type: String,
      required: true,
    },
    employee: {
      type: Object,
      default: () => ({}),
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/leave-entitlements',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      leaveEntitlement: {...leaveEntitlementModel},
      rules: {
        employee: [required],
        leaveType: [required],
        entitlement: [
          required,
          v => {
            return (
              /^\d+(\.\d{1,2})?$/.test(v) ||
              'Should be a number with upto 2 decimal places'
            );
          },
          promiseDebounce(this.validateEntitlement, 500),
        ],
      },
      leavePeriods: [],
    };
  },

  methods: {
    onCancel() {
      navigate('/leave/viewLeaveEntitlements');
    },
    onSave() {
      this.isLoading = true;

      const payload = {
        empNumber: this.leaveEntitlement.employee?.id,
        leaveTypeId: this.leaveEntitlement.leaveType?.id,
        fromDate: this.leaveEntitlement.leavePeriod?.startDate,
        toDate: this.leaveEntitlement.leavePeriod?.endDate,
        entitlement: this.leaveEntitlement.entitlement,
      };

      this.http.create(payload).then(() => {
        this.leaveEntitlement = {...leaveEntitlementModel};
        this.$toast.updateSuccess();
        this.onCancel();
      });
    },

    validateEntitlement(value) {
      return new Promise(resolve => {
        if (value.trim()) {
          this.http
            .request({
              method: 'GET',
              url: `api/v2/leave/leave-entitlements/${this.entitlementId}/validation/entitlements`,
              params: {
                entitlement: value,
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve('Used amount exceeds the current amount');
            });
        } else {
          resolve(true);
        }
      });
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({method: 'GET', url: 'api/v2/leave/leave-periods'})
      .then(({data}) => {
        this.leavePeriods = data.data.map((item, index) => {
          return {
            id: index + 1,
            label: `${item.startDate} - ${item.endDate}`,
            startDate: item.startDate,
            endDate: item.endDate,
          };
        });
        return this.http.get(this.entitlementId);
      })
      .then(response => {
        const {data} = response.data;
        this.leaveEntitlement.employee = {
          id: data.employee.empNumber,
          label: `${data.employee.firstName} ${data.employee.lastName}`,
          isPastEmployee: data.employee.terminationId,
        };
        this.leaveEntitlement.leaveType = {
          id: data.leaveType.id,
          label: data.leaveType.name,
        };
        this.leaveEntitlement.leavePeriod = this.leavePeriods.find(item => {
          return item.startDate == data.fromDate && item.endDate == data.toDate;
        });
        this.leaveEntitlement.entitlement = data.entitlement;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
