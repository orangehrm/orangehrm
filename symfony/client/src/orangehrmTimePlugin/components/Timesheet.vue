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
  <oxd-form class="orangehrm-paper-container">
    <div class="orangehrm-timesheet-header">
      <div class="orangehrm-timesheet-header--title">
        <slot name="header-title"></slot>
      </div>
      <div class="orangehrm-timesheet-header--options">
        <slot name="header-options"></slot>
      </div>
    </div>

    <div class="orangehrm-timesheet-body">
      <table class="orangehrm-timesheet-table">
        <thead class="orangehrm-timesheet-table-header">
          <tr class="orangehrm-timesheet-table-header-row">
            <th class="orangehrm-timesheet-table-header-cell --freeze-left">
              {{ $t('time.project') }}
            </th>
            <th class="orangehrm-timesheet-table-header-cell">
              {{ $t('time.activity') }}
            </th>

            <!-- timesheet days of week -->
            <th
              v-for="day in daysOfWeek"
              :key="day.id"
              class="orangehrm-timesheet-table-header-cell"
            >
              <span class="--day">
                {{ day.day }}
              </span>
              <span>
                {{ day.title }}
              </span>
            </th>
            <!-- timesheet days of week -->

            <th
              v-if="totals"
              class="orangehrm-timesheet-table-header-cell --freeze-right"
            >
              {{ $t('general.total') }}
            </th>
          </tr>
        </thead>

        <tbody class="orangehrm-timesheet-table-body">
          <!-- timesheet activities -->
          <tr
            v-for="record in records"
            :key="record.id"
            class="orangehrm-timesheet-table-body-row"
          >
            <td class="orangehrm-timesheet-table-body-cell --freeze-left">
              {{ record.project }}
            </td>
            <td class="orangehrm-timesheet-table-body-cell">
              {{ record.activity }}
            </td>
            <td
              v-for="day in record.days"
              :key="day.id"
              :class="{
                'orangehrm-timesheet-table-body-cell': true,
                '--highlight-3': !editable && !day.workday,
              }"
            >
              {{ day.trackedTime ?? '00:00' }}
            </td>
            <td
              v-if="totals"
              class="orangehrm-timesheet-table-body-cell --freeze-right --highlight"
            >
              {{ record.totalTrackedTime }}
            </td>
          </tr>
          <!-- timesheet activities -->

          <!-- totals -->
          <tr v-if="totals" class="orangehrm-timesheet-table-body-row --total">
            <td
              class="orangehrm-timesheet-table-body-cell --freeze-left --highlight"
            >
              {{ $t('general.total') }}
            </td>
            <td></td>
            <!-- total per day -->
            <td
              v-for="(total, key) in totals"
              :key="key"
              class="orangehrm-timesheet-table-body-cell"
            >
              {{ total }}
            </td>
            <!-- total per day -->
            <td
              class="orangehrm-timesheet-table-body-cell --freeze-right --highlight-2"
            >
              {{ subtotal }}
            </td>
          </tr>
          <!-- totals -->
        </tbody>
      </table>
    </div>

    <div class="orangehrm-timesheet-footer">
      <div class="orangehrm-timesheet-footer--title">
        <slot name="footer-title"></slot>
      </div>
      <div class="orangehrm-timesheet-footer--options">
        <slot name="footer-options"></slot>
      </div>
    </div>
  </oxd-form>
</template>

<script>
import {parseDate} from '@ohrm/core/util/helper/datefns';

export default {
  name: 'timesheet',

  props: {
    days: {
      type: Array,
      default: () => [],
    },
    records: {
      type: Array,
      default: () => [],
    },
    totals: {
      type: Object,
      required: false,
    },
    subtotal: {
      type: String,
      required: false,
    },
    editable: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    daysOfWeek() {
      const days = [
        this.$t('general.sun'),
        this.$t('general.mon'),
        this.$t('general.tue'),
        this.$t('general.wed'),
        this.$t('general.thu'),
        this.$t('general.fri'),
        this.$t('general.sat'),
      ];
      return Array.isArray(this.days)
        ? this.days.map(day => {
            const date = parseDate(day, 'yyyy-MM-dd');
            return {
              id: date.valueOf(),
              day: date.getDate(),
              title: days[date.getDay()],
            };
          })
        : [];
    },
  },
};
</script>

<style src="./timesheet.scss" lang="scss" scoped></style>
