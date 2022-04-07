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
        {{ $t('leave.add_leave_entitlement') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group
                :label="$t('leave.add_to')"
                :classes="{wrapper: '--grouped-field'}"
              >
                <oxd-input-field
                  v-model="leaveEntitlement.bulkAssign"
                  type="radio"
                  :option-label="$t('leave.individual_employee')"
                  value="0"
                />
                <oxd-input-field
                  v-model="leaveEntitlement.bulkAssign"
                  type="radio"
                  :option-label="$t('leave.multiple_employees')"
                  value="1"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row v-if="leaveEntitlement.bulkAssign == 0">
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="leaveEntitlement.employee"
                :params="{
                  includeEmployees: 'currentAndPast',
                }"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row v-else>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="leaveEntitlement.location"
                type="select"
                :label="$t('general.location')"
                :options="locations"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="leaveEntitlement.subunit"
                type="select"
                :label="$t('general.sub_unit')"
                :options="subunits"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-leave-entitled">
              <oxd-text class="orangehrm-leave-entitled-text" type="subtitle-2">
                {{
                  $t('leave.matches_emp_count_employees', {
                    empMatchCount: empMatchCount,
                  })
                }}
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <leave-type-dropdown
                v-model="leaveEntitlement.leaveType"
                :empty-text="$t('leave.no_leave_types_defined')"
                :rules="rules.leaveType"
                :eligible-only="false"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <leave-period-dropdown
                v-model="leaveEntitlement.leavePeriod"
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

    <entitlement-update-modal
      ref="updateModal"
      :data="leaveEntitlement"
    ></entitlement-update-modal>
    <entitlement-bulk-update-modal
      ref="bulkUpdateModal"
      :data="leaveEntitlement"
    ></entitlement-bulk-update-modal>
    <entitlement-no-match-modal ref="noMatchModal"></entitlement-no-match-modal>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {required, max} from '@/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import LeavePeriodDropdown from '@/orangehrmLeavePlugin/components/LeavePeriodDropdown';
import EntitlementUpdateModal from '@/orangehrmLeavePlugin/components/EntitlementUpdateModal';
import EntitlementBulkUpdateModal from '@/orangehrmLeavePlugin/components/EntitlementBulkUpdateModal';
import EntitlementNoMatchModal from '@/orangehrmLeavePlugin/components/EntitlementNoMatchModal';

const leaveEntitlementModel = {
  bulkAssign: 0,
  employee: null,
  leaveType: null,
  leavePeriod: null,
  entitlement: '',
  subunit: null,
  location: null,
};

export default {
  components: {
    'leave-type-dropdown': LeaveTypeDropdown,
    'leave-period-dropdown': LeavePeriodDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
    'entitlement-update-modal': EntitlementUpdateModal,
    'entitlement-bulk-update-modal': EntitlementBulkUpdateModal,
    'entitlement-no-match-modal': EntitlementNoMatchModal,
  },

  props: {
    locations: {
      type: Array,
      default: () => [],
    },
    subunits: {
      type: Array,
      default: () => [],
    },
    leavePeriod: {
      type: Object,
      required: false,
      default: () => null,
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
      leavePeriodDefined: false,
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
        ],
      },
      empMatchCount: 0,
    };
  },

  watch: {
    'leaveEntitlement.location': 'fetchEmployeeCount',
    'leaveEntitlement.subunit': 'fetchEmployeeCount',
  },

  beforeMount() {
    this.fetchEmployeeCount();
    if (this.leavePeriod) {
      this.leaveEntitlement.leavePeriod = this.leavePeriod;
    }
  },

  methods: {
    onCancel() {
      navigate('/leave/viewLeaveEntitlements');
    },
    async onSave() {
      let confirmation = null;
      this.isLoading = true;
      const isBulkAssign = this.leaveEntitlement.bulkAssign == 1;

      if (isBulkAssign) {
        if (this.empMatchCount === 0) {
          this.isLoading = false;
          return this.$refs.noMatchModal.showDialog();
        }
        confirmation = await this.$refs.bulkUpdateModal.showDialog();
      } else {
        confirmation = await this.$refs.updateModal.showDialog();
      }

      if (confirmation !== 'ok') {
        this.isLoading = false;
        return;
      }

      const payload = {
        empNumber: undefined,
        bulkAssign: undefined,
        locationId: undefined,
        subunitId: undefined,
        leaveTypeId: this.leaveEntitlement.leaveType?.id,
        fromDate: this.leaveEntitlement.leavePeriod?.startDate,
        toDate: this.leaveEntitlement.leavePeriod?.endDate,
        entitlement: this.leaveEntitlement.entitlement,
      };
      if (isBulkAssign) {
        payload.bulkAssign = true;
        payload.locationId = this.leaveEntitlement.location?.id;
        payload.subunitId = this.leaveEntitlement.subunit?.id;
      } else {
        payload.empNumber = this.leaveEntitlement.employee?.id;
      }
      this.http
        .create(payload)
        .then(response => {
          let toast = null;
          let params = null;
          const {data} = response.data;
          if (Array.isArray(data)) {
            toast = this.$toast.success({
              title: this.$t('general.success'),
              message: this.$t('leave.entitlement_added_to_n_employees', {
                count: data.length,
              }),
            });
          } else {
            params = {
              empNumber: data.employee.empNumber,
              leaveTypeId: data.leaveType.id,
              startDate: data.fromDate,
              endDate: data.toDate,
            };
            toast = this.$toast.saveSuccess();
          }
          return new Promise(resolve => {
            toast.then(() => {
              resolve(params);
            });
          });
        })
        .then(params => {
          if (params) {
            navigate('/leave/viewLeaveEntitlements', undefined, params);
          } else {
            navigate('/leave/viewLeaveEntitlements');
          }
        });
    },
    async fetchEmployeeCount() {
      this.http
        .request({
          method: 'GET',
          url: 'api/v2/pim/employees/count',
          params: {
            locationId: this.leaveEntitlement.location?.id,
            subunitId: this.leaveEntitlement.subunit?.id,
          },
        })
        .then(response => {
          const {data} = response.data;
          this.empMatchCount = parseInt(data.count);
        });
    },
  },
};
</script>

<style src="./add-entitlement.scss" lang="scss" scoped></style>
