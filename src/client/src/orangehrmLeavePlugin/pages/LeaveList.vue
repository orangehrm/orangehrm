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
  <leave-list-table :leave-statuses="leaveStatuses">
    <template #default="{filters, filterItems, rules, onReset}">
      <oxd-table-filter :filter-title="$t('leave.leave_list')">
        <oxd-form @submitValid="filterItems" @reset="onReset">
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <date-input
                  v-model="filters.fromDate"
                  :label="$t('general.from_date')"
                  :rules="rules.fromDate"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <date-input
                  v-model="filters.toDate"
                  :label="$t('general.to_date')"
                  :rules="rules.toDate"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="filters.statuses"
                  value="Select"
                  type="multiselect"
                  :label="$t('leave.show_leave_with_status')"
                  :options="leaveStatuses"
                  :rules="rules.statuses"
                  required
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <leave-type-dropdown
                  v-model="filters.leaveType"
                  :eligible-only="false"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <employee-autocomplete
                  v-model="filters.employee"
                  :params="{
                    includeEmployees: filters.includePastEmps
                      ? 'currentAndPast'
                      : 'onlyCurrent',
                  }"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="filters.subunit"
                  type="select"
                  :label="$t('general.sub_unit')"
                  :options="subunits"
                />
              </oxd-grid-item>

              <oxd-grid-item class="orangehrm-leave-filter --span-column-2">
                <oxd-text class="orangehrm-leave-filter-text" tag="p">
                  {{ $t('leave.include_past_employees') }}
                </oxd-text>
                <oxd-switch-input v-model="filters.includePastEmps" />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <required-text />
            <oxd-button
              display-type="ghost"
              :label="$t('general.reset')"
              type="reset"
            />
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
  </leave-list-table>
</template>

<script>
import LeaveListTable from '@/orangehrmLeavePlugin/components/LeaveListTable';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';

export default {
  components: {
    'leave-list-table': LeaveListTable,
    'employee-autocomplete': EmployeeAutocomplete,
    'oxd-switch-input': SwitchInput,
    'leave-type-dropdown': LeaveTypeDropdown,
  },
  props: {
    subunits: {
      type: Array,
      default: () => [],
    },
    leaveStatuses: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-leave-filter {
  display: flex;
  align-items: center;
  white-space: nowrap;
  &-text {
    font-size: $oxd-input-control-font-size;
    margin-right: 1rem;
  }
}
</style>
