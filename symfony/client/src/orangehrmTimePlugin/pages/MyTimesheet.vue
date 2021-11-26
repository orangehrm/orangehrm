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
    <timesheet
      :days="meta.days"
      :records="data"
      :totals="meta.totals"
      :subtotal="meta.subtotal"
    >
      <template v-slot:header-title>
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('time.my_timesheet') }}
        </oxd-text>
      </template>
      <template v-slot:header-options>
        <timesheet-period></timesheet-period>
        <oxd-button
          iconName="plus"
          displayType="ghost"
          :label="$t('time.add_timesheet')"
        />
      </template>
      <template v-slot:footer-title>
        <oxd-text type="subtitle-2"> {{ $t('general.status') }}: </oxd-text>
      </template>
      <template v-slot:footer-options>
        <oxd-button displayType="ghost" :label="$t('general.edit')" />
        <submit-button :label="$t('general.submit')" />
      </template>
    </timesheet>
  </div>
</template>

<script>
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';
import TimesheetPeriod from '@/orangehrmTimePlugin/components/TimesheetPeriod.vue';

export default {
  components: {
    timesheet: Timesheet,
    'timesheet-period': TimesheetPeriod,
  },

  data() {
    return {
      data: [
        {
          id: 100,
          project: 'Manhattan Project',
          activity: 'Nuclear Fission',
          totalTrackedTime: '05:00',
          days: [
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
          days: [
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
        days: [
          '2021-11-22',
          '2021-11-23',
          '2021-11-24',
          '2021-11-25',
          '2021-11-26',
          '2021-11-27',
          '2021-11-28',
        ],
      },
    };
  },
};
</script>
