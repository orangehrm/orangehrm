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
  <reports-table
    module="leave"
    :name="filters.type"
    :filters="serializedFilters"
    :column-count="6"
  >
    <template #default="{generateReport}">
      <oxd-table-filter
        :filter-title="$t('leave.leave_entitlement_and_usage_report')"
      >
        <oxd-form @submitValid="generateReport">
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-group
                  :label="$t('leave.generate_for')"
                  :classes="{wrapper: '--grouped-field'}"
                >
                  <oxd-input-field
                    v-model="filters.type"
                    type="radio"
                    :option-label="$t('leave.leave_type')"
                    value="leave_type_leave_entitlements_and_usage"
                  />
                  <oxd-input-field
                    v-model="filters.type"
                    type="radio"
                    :option-label="$t('general.employee')"
                    value="employee_leave_entitlements_and_usage"
                  />
                </oxd-input-group>
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-form-row
            v-if="filters.type === 'leave_type_leave_entitlements_and_usage'"
          >
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <leave-type-dropdown
                  v-model="filters.leaveType"
                  :empty-text="$t('leave.no_leave_types_defined')"
                  :eligible-only="false"
                  :show-empty-selector="false"
                  :rules="rules.leaveType"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <leave-period-dropdown
                  v-model="filters.leavePeriod"
                  :rules="rules.leavePeriod"
                  required
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="filters.location"
                  type="select"
                  :label="$t('general.location')"
                  :options="locations"
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
              <oxd-grid-item>
                <jobtitle-dropdown v-model="filters.jobTitle" />
              </oxd-grid-item>
              <oxd-grid-item class="orangehrm-leave-filter --span-column-2">
                <oxd-text class="orangehrm-leave-filter-text" tag="p">
                  {{ $t('leave.include_past_employees') }}
                </oxd-text>
                <oxd-switch-input v-model="filters.includePastEmps" />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-form-row v-else>
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
                <leave-period-dropdown
                  v-model="filters.leavePeriod"
                  :rules="rules.leavePeriod"
                  required
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <required-text />
            <oxd-button
              type="submit"
              display-type="secondary"
              class="orangehrm-left-space"
              :label="$t('general.generate')"
            />
          </oxd-form-actions>
        </oxd-form>
      </oxd-table-filter>
      <br />
    </template>
  </reports-table>
</template>

<script>
import {computed, ref} from 'vue';
import {required} from '@/core/util/validation/rules';
import ReportsTable from '@/core/components/table/ReportsTable';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import LeavePeriodDropdown from '@/orangehrmLeavePlugin/components/LeavePeriodDropdown';

const defaultFilters = {
  type: 'leave_type_leave_entitlements_and_usage',
  employee: null,
  leavePeriod: null,
  leaveType: null,
  subunit: null,
  location: null,
  jobTitle: null,
  includePastEmps: false,
};

export default {
  components: {
    'reports-table': ReportsTable,
    'oxd-switch-input': SwitchInput,
    'jobtitle-dropdown': JobtitleDropdown,
    'leave-type-dropdown': LeaveTypeDropdown,
    'leave-period-dropdown': LeavePeriodDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
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

  setup(props) {
    const filters = ref({
      ...defaultFilters,
      ...(props.leavePeriod && {leavePeriod: props.leavePeriod}),
    });
    const rules = ref({
      employee: [required],
      leavePeriod: [required],
      leaveType: [required],
    });

    const serializedFilters = computed(() => {
      if (filters.value.type === 'leave_type_leave_entitlements_and_usage') {
        return {
          name: filters.value.type,
          fromDate: filters.value.leavePeriod?.startDate,
          toDate: filters.value.leavePeriod?.endDate,
          subunitId: filters.value.subunit?.id,
          leaveTypeId: filters.value.leaveType?.id,
          locationId: filters.value.location?.id,
          jobTitleId: filters.value.jobTitle?.id,
          includeEmployees: filters.value.includePastEmps
            ? 'currentAndPast'
            : 'onlyCurrent',
        };
      } else {
        return {
          name: filters.value.type,
          empNumber: filters.value.employee?.id,
          fromDate: filters.value.leavePeriod?.startDate,
          toDate: filters.value.leavePeriod?.endDate,
        };
      }
    });

    return {
      rules,
      filters,
      serializedFilters,
    };
  },
};
</script>

<style src="./leave-entitlement-report.scss" lang="scss" scoped></style>
