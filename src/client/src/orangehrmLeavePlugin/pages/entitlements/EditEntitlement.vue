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
                :eligible-only="false"
                required
                disabled
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="leaveEntitlement.leavePeriod"
                type="select"
                :label="$t('leave.leave_period')"
                :options="leavePeriods"
                :rules="rules.leavePeriod"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="leaveEntitlement.entitlement"
                :rules="rules.entitlement"
                :label="$t('leave.entitlement')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
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
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {required, max} from '@/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';

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
    http.setIgnorePath(
      'api/v2/leave/leave-entitlements/[0-9]+/validation/entitlements',
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
        leavePeriod: [required],
        entitlement: [
          required,
          v => {
            return (
              /^\d+(\.\d{1,2})?$/.test(v) ||
              this.$t('leave.should_be_a_number_with_2_decimal_places')
            );
          },
          max(10000),
          promiseDebounce(this.validateEntitlement, 500),
        ],
      },
      leavePeriods: [],
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({method: 'GET', url: 'api/v2/leave/leave-periods'})
      .then(({data}) => {
        this.leavePeriods = data.data.map(item => {
          return {
            id: `${item.startDate}_${item.endDate}`,
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
          return item.id === `${data.fromDate}_${data.toDate}`;
        });
        this.leaveEntitlement.entitlement = data.entitlement;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/leave/viewLeaveEntitlements', undefined, {
        empNumber: this.leaveEntitlement.employee?.id,
        leaveTypeId: this.leaveEntitlement.leaveType?.id,
        startDate: this.leaveEntitlement.leavePeriod?.startDate,
        endDate: this.leaveEntitlement.leavePeriod?.endDate,
      });
    },
    onSave() {
      this.isLoading = true;

      const payload = {
        fromDate: this.leaveEntitlement.leavePeriod?.startDate,
        toDate: this.leaveEntitlement.leavePeriod?.endDate,
        entitlement: this.leaveEntitlement.entitlement,
      };

      this.http.update(this.entitlementId, payload).then(() => {
        this.$toast.updateSuccess();
        this.onCancel();
      });
    },

    validateEntitlement(value) {
      const entitlement = parseFloat(value);
      return new Promise(resolve => {
        if (!isNaN(entitlement)) {
          this.http
            .request({
              method: 'GET',
              url: `api/v2/leave/leave-entitlements/${this.entitlementId}/validation/entitlements`,
              params: {
                entitlement,
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve(
                    this.$t('leave.used_amount_exceeds_the_current_amount'),
                  );
            });
        } else {
          resolve(true);
        }
      });
    },
  },
};
</script>
