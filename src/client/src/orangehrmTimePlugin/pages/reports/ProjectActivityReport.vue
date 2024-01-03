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
    name="project"
    :prefetch="project !== null"
    :filters="serializedFilters"
    :column-count="2"
  >
    <template #default="{generateReport}">
      <oxd-table-filter :filter-title="$t('time.project_report')">
        <oxd-form @submit-valid="generateReport">
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <project-autocomplete
                  v-model="filters.project"
                  :rules="rules.project"
                  :label="$t('time.project_name')"
                  required
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <date-input
                  v-model="filters.fromDate"
                  :placeholder="$t('general.from')"
                  :rules="rules.fromDate"
                  :label="$t('time.project_date_range')"
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
              <oxd-grid-item class="orangehrm-switch-filter --span-column-2">
                <oxd-text class="orangehrm-switch-filter-text" tag="p">
                  {{ $t('time.only_include_approved_timesheets') }}
                </oxd-text>
                <oxd-switch-input v-model="filters.includeTimesheet" />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <required-text />
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
  required,
  validSelection,
  validDateFormat,
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
} from '@/core/util/validation/rules';
import ReportsTable from '@/core/components/table/ReportsTable';
import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';
import usei18n from '@/core/util/composable/usei18n';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {OxdSwitchInput} from '@ohrm/oxd';

const defaultFilters = {
  project: null,
  fromDate: null,
  toDate: null,
  includeTimesheet: false,
};

export default {
  components: {
    'reports-table': ReportsTable,
    'oxd-switch-input': OxdSwitchInput,
    'project-autocomplete': ProjectAutocomplete,
  },

  props: {
    project: {
      type: Object,
      required: false,
      default: null,
    },
    fromDate: {
      type: String,
      required: false,
      default: null,
    },
    toDate: {
      type: String,
      required: false,
      default: null,
    },
    includeTimesheet: {
      type: Boolean,
      default: false,
    },
  },

  setup(props) {
    const filters = ref({
      ...defaultFilters,
      fromDate: props.fromDate,
      toDate: props.toDate,
      includeTimesheet: props.includeTimesheet,
      ...(props.project && {project: props.project}),
    });

    const {$t} = usei18n();
    const {userDateFormat} = useDateFormat();

    const rules = {
      project: [required, validSelection],
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
    };

    const serializedFilters = computed(() => {
      return {
        projectId: filters.value.project?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        includeTimesheet: filters.value.includeTimesheet
          ? 'onlyApproved'
          : 'all',
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

<style src="./time-reports.scss" lang="scss" scoped></style>
