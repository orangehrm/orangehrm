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
  <reports-table
    module="time"
    name="attendance"
    :prefetch="false"
    :filters="serializedFilters"
    :column-count="2"
  >
    <template #default="{generateReport}">
      <oxd-table-filter
        :filter-title="$t('attendance.attendance_total_summary_report')"
      >
        <oxd-form @submit-valid="generateReport">
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <employee-autocomplete
                  v-model="filters.employee"
                  :rules="rules.employee"
                  :params="{
                    includeEmployees: 'currentAndPast',
                  }"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <jobtitle-dropdown v-model="filters.jobTitle" />
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
                <employment-status-dropdown v-model="filters.empStatus" />
              </oxd-grid-item>
              <oxd-grid-item>
                <date-input
                  v-model="filters.fromDate"
                  :placeholder="$t('general.from')"
                  :rules="rules.fromDate"
                  :label="$t('general.date_range')"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <date-input
                  v-model="filters.toDate"
                  label="&nbsp"
                  :placeholder="$t('general.to')"
                  :rules="rules.toDate"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <oxd-button
              type="submit"
              display-type="secondary"
              :label="$t('general.view')"
            />
          </oxd-form-actions>
        </oxd-form>
      </oxd-table-filter>
      <br />
    </template>

    <template #footer="{data}">
      {{ $t('time.total_duration') }}:
      {{ data.meta ? data.meta.sum.label : '0.00' }}
    </template>
  </reports-table>
</template>

<script>
import {computed, ref} from 'vue';
import {
  validSelection,
  validDateFormat,
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import ReportsTable from '@/core/components/table/ReportsTable';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import EmploymentStatusDropdown from '@/orangehrmPimPlugin/components/EmploymentStatusDropdown';
import usei18n from '@/core/util/composable/usei18n';
import useDateFormat from '@/core/util/composable/useDateFormat';

const defaultFilters = {
  employee: null,
  fromDate: null,
  toDate: null,
  jobTitle: null,
  subunit: null,
  empStatus: null,
};

export default {
  components: {
    'reports-table': ReportsTable,
    'jobtitle-dropdown': JobtitleDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
    'employment-status-dropdown': EmploymentStatusDropdown,
  },

  props: {
    subunits: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const filters = ref({
      ...defaultFilters,
    });
    const {$t} = usei18n();
    const {userDateFormat} = useDateFormat();

    const rules = {
      fromDate: [
        validDateFormat(userDateFormat),
        startDateShouldBeBeforeEndDate(
          () => filters.value.toDate,
          $t('general.from_date_should_be_before_to_date'),
          {allowSameDate: true},
        ),
      ],
      toDate: [
        validDateFormat(userDateFormat),
        endDateShouldBeAfterStartDate(
          () => filters.value.fromDate,
          $t('general.to_date_should_be_after_from_date'),
          {allowSameDate: true},
        ),
      ],
      employee: [shouldNotExceedCharLength(100), validSelection],
    };

    const serializedFilters = computed(() => {
      return {
        empNumber: filters.value.employee?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        jobTitleId: filters.value.jobTitle?.id,
        subunitId: filters.value.subunit?.id,
        employmentStatusId: filters.value.empStatus?.id,
      };
    });

    return {
      rules,
      filters,
      serializedFilters,
    };
  },
};
</script>
