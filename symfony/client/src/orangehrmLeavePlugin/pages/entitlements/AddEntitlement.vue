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
                  type="radio"
                  v-model="leaveEntitlement.isMultiple"
                  :optionLabel="$t('leave.individual_employee')"
                  value="0"
                />
                <oxd-input-field
                  type="radio"
                  v-model="leaveEntitlement.isMultiple"
                  :optionLabel="$t('leave.multiple_employees')"
                  value="1"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row v-if="leaveEntitlement.isMultiple == 0">
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="leaveEntitlement.employee"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row v-else>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <location-dropdown
                v-model="leaveEntitlement.location"
                :rules="rules.location"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <subunit-dropdown
                v-model="leaveEntitlement.subunit"
                :rules="rules.subunit"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-text type="subtitle-2">
                Matches 40 Employees
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <leave-type-dropdown
                v-model="leaveEntitlement.type"
                :rules="rules.type"
                :eligible-only="true"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :rules="rules.period"
                :options="leavePeriods"
                :label="$t('leave.leave_period')"
                v-model="leaveEntitlement.period"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :rules="rules.amount"
                :label="$t('leave.entitlement')"
                v-model="leaveEntitlement.amount"
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
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown';
import LocationDropdown from '@/orangehrmLeavePlugin/components/LocationDropdown';

const leaveEntitlementModel = {
  isMultiple: 0,
  employee: null,
  type: null,
  period: null,
  amount: '',
  subunit: null,
  location: null,
};

export default {
  data() {
    return {
      isLoading: false,
      leaveEntitlement: {...leaveEntitlementModel},
      leavePeriodDefined: false,
      rules: {
        employee: [required],
        type: [required],
        amount: [required],
        subunit: [required],
        location: [required],
      },
      leavePeriods: [],
    };
  },

  components: {
    'leave-type-dropdown': LeaveTypeDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
    'subunit-dropdown': SubunitDropdown,
    'location-dropdown': LocationDropdown,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/leave-period',
    );
    return {
      http,
    };
  },

  methods: {
    onCancel() {
      navigate('/');
    },
    onSave() {
      this.isLoading = true;
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.--grouped-field) {
  display: flex;
}
</style>
