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
      :loading="isLoading"
      :records="timesheet.data"
      :days="timesheet.meta.days"
      :totals="timesheet.meta.totals"
      :subtotal="timesheet.meta.subtotal"
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
          @click="onClickAddTimesheet"
        />
      </template>
      <template v-slot:footer-title>
        <oxd-text type="subtitle-2">
          {{ $t('general.status') }}: Not Submitted
        </oxd-text>
      </template>
      <template v-slot:footer-options>
        <oxd-button displayType="ghost" :label="$t('general.edit')" />
        <oxd-button
          type="submit"
          displayType="secondary"
          :label="$t('general.submit')"
        />
      </template>
    </timesheet>
    <br />
    <timesheet-actions></timesheet-actions>
    <add-timesheet-modal
      v-if="showSaveModal"
      @close="onSaveModalClose"
    ></add-timesheet-modal>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';
import TimesheetPeriod from '@/orangehrmTimePlugin/components/TimesheetPeriod.vue';
import TimesheetActions from '@/orangehrmTimePlugin/components/TimesheetActions.vue';
import AddTimesheetModal from '@/orangehrmTimePlugin/components/AddTimesheetModal.vue';

const myTimesheetModal = {
  data: [],
  meta: {},
};

export default {
  components: {
    timesheet: Timesheet,
    'timesheet-period': TimesheetPeriod,
    'timesheet-actions': TimesheetActions,
    'add-timesheet-modal': AddTimesheetModal,
  },

  setup() {
    const http = new APIService(
      //   window.appGlobal.baseUrl,
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      '/api/v2/time/my-timesheet',
    );

    return {http};
  },

  data() {
    return {
      isLoading: false,
      timesheet: {...myTimesheetModal},
      showSaveModal: false,
    };
  },

  methods: {
    onClickAddTimesheet() {
      this.showSaveModal = true;
    },
    onSaveModalClose() {
      this.showSaveModal = false;
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(1)
      .then(response => {
        const {data, meta} = response.data;
        this.timesheet = {data, meta};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
