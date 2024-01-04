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
    module="leave"
    name="my_leave_entitlements_and_usage"
    :prefetch="true"
    :filters="serializedFilters"
    :column-count="6"
  >
    <template #default="{generateReport}">
      <oxd-table-filter
        :filter-title="$t('leave.my_leave_entitlement_and_usage_report')"
      >
        <oxd-form @submit-valid="generateReport">
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
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
import LeavePeriodDropdown from '@/orangehrmLeavePlugin/components/LeavePeriodDropdown';

export default {
  components: {
    'reports-table': ReportsTable,
    'leave-period-dropdown': LeavePeriodDropdown,
  },
  props: {
    leavePeriod: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },

  setup(props) {
    const filters = ref({
      leavePeriod: props.leavePeriod ? props.leavePeriod : null,
    });
    const rules = ref({
      leavePeriod: [required],
    });

    const serializedFilters = computed(() => {
      return {
        fromDate: filters.value.leavePeriod?.startDate,
        toDate: filters.value.leavePeriod?.endDate,
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
