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
  <leave-list-table :my-leave-list="true">
    <template v-slot:default="{filters, filterItems, rules}">
      <oxd-table-filter :filter-title="$t('leave.my_leave_list')">
        <oxd-form @submitValid="filterItems">
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <date-input
                  :label="$t('general.from_date')"
                  v-model="filters.fromDate"
                  :rules="rules.fromDate"
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <date-input
                  :label="$t('general.to_date')"
                  v-model="filters.toDate"
                  :rules="rules.toDate"
                />
              </oxd-grid-item>
              <oxd-grid-item class="--span-column-2">
                <oxd-input-field
                  value="Select"
                  type="multiselect"
                  :label="$t('leave.show_leave_with_status')"
                  v-model="filters.statuses"
                  :options="leaveStatuses"
                  :rules="rules.statuses"
                  required
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <required-text />
            <oxd-button
              class="orangehrm-left-space"
              displayType="secondary"
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

export default {
  components: {
    'leave-list-table': LeaveListTable,
  },
  props: {
    leaveStatuses: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
