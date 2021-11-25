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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-timesheet-header">
        <div class="orangehrm-timesheet-header--title">
          <oxd-text tag="h6" class="orangehrm-main-title">
            {{ $t('time.my_timesheet') }}
          </oxd-text>
        </div>
        <div class="orangehrm-timesheet-header--options">
          <oxd-button
            iconName="plus"
            displayType="ghost"
            :label="$t('time.add_timesheet')"
          />
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
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.mon') }}
              </th>

              <!-- timesheet days of week -->
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.tue') }}
              </th>
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.wed') }}
              </th>
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.thu') }}
              </th>
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.fri') }}
              </th>
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.sat') }}
              </th>
              <th class="orangehrm-timesheet-table-header-cell">
                {{ $t('general.sun') }}
              </th>
              <!-- timesheet days of week -->

              <th class="orangehrm-timesheet-table-header-cell --freeze-right">
                {{ $t('general.total') }}
              </th>
            </tr>
          </thead>

          <tbody class="orangehrm-timesheet-table-body">
            <!-- timesheet activities -->
            <tr
              v-for="item in data"
              :key="item.id"
              class="orangehrm-timesheet-table-body-row"
            >
              <td class="orangehrm-timesheet-table-body-cell --freeze-left">
                {{ item.project }}
              </td>
              <td class="orangehrm-timesheet-table-body-cell">
                {{ item.activity }}
              </td>
              <td
                v-for="record in item.records"
                :key="record.id"
                :class="{
                  'orangehrm-timesheet-table-body-cell': true,
                  '--highlight-3': !record.workday,
                }"
              >
                {{ record.trackedTime ?? '00:00' }}
              </td>
              <td class="orangehrm-timesheet-table-body-cell --freeze-right --highlight">
                {{ item.totalTrackedTime }}
              </td>
            </tr>
            <!-- timesheet activities -->

            <!-- totals -->
            <tr class="orangehrm-timesheet-table-body-row --total">
              <td
                class="orangehrm-timesheet-table-body-cell --freeze-left --highlight"
              >
                {{ $t('general.total') }}
              </td>
              <td></td>
              <!-- total per day -->
              <td
                v-for="(total, key) in meta.totals"
                :key="key"
                class="orangehrm-timesheet-table-body-cell"
              >
                {{ total }}
              </td>
              <!-- total per day -->
              <td
                class="orangehrm-timesheet-table-body-cell --freeze-right --highlight-2"
              >
                {{ meta.subtotal ?? '00:00' }}
              </td>
            </tr>
            <!-- totals -->
          </tbody>
        </table>
      </div>

      <div class="orangehrm-timesheet-footer">
        <div class="orangehrm-timesheet-header--title">
          <oxd-text type="subtitle-2"> {{ $t('general.status') }}: </oxd-text>
        </div>
        <div class="orangehrm-timesheet-header--options">
          <oxd-button displayType="ghost" :label="$t('general.edit')" />
          <submit-button :label="$t('general.submit')" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'my-timesheet',

  data() {
    return {
      data: [
        {
          id: 100,
          project: 'Manhattan Project',
          activity: 'Nuclear Fission',
          totalTrackedTime: '05:00',
          records: [
            {
              id: 1,
              date: '2021-11-22',
              comment: 'test',
              trackedTime: '05:00',
              workday: true,
            },
            {
              id: 2,
              date: '2021-11-23',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 3,
              date: '2021-11-24',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 4,
              date: '2021-11-25',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 5,
              date: '2021-11-26',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 6,
              date: '2021-11-27',
              comment: null,
              trackedTime: null,
              workday: false,
            },
            {
              id: 7,
              date: '2021-11-28',
              comment: null,
              trackedTime: null,
              workday: false,
            },
          ],
        },
        {
          id: 101,
          project: 'Manhattan Project',
          activity: 'Fat Boy',
          totalTrackedTime: '00:00',
          records: [
            {
              id: 8,
              date: '2021-11-22',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 9,
              date: '2021-11-23',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 10,
              date: '2021-11-24',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 11,
              date: '2021-11-25',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 12,
              date: '2021-11-26',
              comment: null,
              trackedTime: null,
              workday: true,
            },
            {
              id: 13,
              date: '2021-11-27',
              comment: null,
              trackedTime: null,
              workday: false,
            },
            {
              id: 14,
              date: '2021-11-28',
              comment: null,
              trackedTime: null,
              workday: false,
            },
          ],
        },
      ],
      meta: {
        subtotal: '05:00',
        totals: {
          '2021-11-22': '05:00',
          '2021-11-23': '00:00',
          '2021-11-24': '00:00',
          '2021-11-25': '00:00',
          '2021-11-26': '00:00',
          '2021-11-27': '00:00',
          '2021-11-28': '00:00',
        },
      },
    };
  },
};
</script>

<style src="./timesheet.scss" lang="scss" scoped></style>
