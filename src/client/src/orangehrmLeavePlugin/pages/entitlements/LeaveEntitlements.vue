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
  <leave-entitlement-table :prefetch="false">
    <template #default="{filters, filterItems}">
      <oxd-table-filter :filter-title="$t('leave.leave_entitlements')">
        <oxd-form @submitValid="filterItems">
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <employee-autocomplete
                  v-model="filters.employee"
                  :rules="rules.employee"
                  :params="{
                    includeEmployees: 'currentAndPast',
                  }"
                  required
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <leave-type-dropdown
                  v-model="filters.leaveType"
                  :eligible-only="false"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <leave-period-dropdown
                  v-model="filters.leavePeriod"
                  :show-empty-selector="false"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <required-text />
            <oxd-button
              class="orangehrm-left-space"
              display-type="secondary"
              :label="$t('general.search')"
              type="submit"
            />
          </oxd-form-actions>
        </oxd-form>
      </oxd-table-filter>
    </template>
  </leave-entitlement-table>
</template>

<script>
import {required} from '@/core/util/validation/rules';
import LeaveEntitlementTable from '@/orangehrmLeavePlugin/components/LeaveEntitlementTable';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import LeavePeriodDropdown from '@/orangehrmLeavePlugin/components/LeavePeriodDropdown';

export default {
  components: {
    'leave-entitlement-table': LeaveEntitlementTable,
    'employee-autocomplete': EmployeeAutocomplete,
    'leave-type-dropdown': LeaveTypeDropdown,
    'leave-period-dropdown': LeavePeriodDropdown,
  },
  data() {
    return {
      rules: {
        employee: [required],
      },
    };
  },
};
</script>
