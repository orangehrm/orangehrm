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
      v-model:records="timesheet.data"
      :editable="true"
      :loading="isLoading"
      :days="timesheet.meta.days"
    >
      <template #header-title>
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('time.my_timesheet') }}
        </oxd-text>
      </template>
      <template #header-options>
        <oxd-text tag="p" class="orangehrm-timeperiod-title">
          {{ $t('time.timesheet_period') }}
        </oxd-text>
        <oxd-text tag="h6" class="orangehrm-main-title">
          2021-11-22 - 2021-11-28
        </oxd-text>
      </template>

      <template #footer-title>
        <oxd-text tag="p" class="orangehrm-form-hint">
          * {{ $t('time.deleted_project_activities_are_not_editable') }}
        </oxd-text>
      </template>
      <template #footer-options>
        <oxd-button display-type="ghost" :label="$t('general.cancel')" />
        <oxd-button
          display-type="ghost"
          :label="$t('general.reset')"
          @click="onClickReset"
        />
        <oxd-button
          type="submit"
          display-type="secondary"
          :label="$t('general.save')"
        />
      </template>
    </timesheet>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';

const myTimesheetModal = {
  data: [],
  meta: {},
};

export default {
  components: {
    timesheet: Timesheet,
  },

  props: {
    timesheetId: {
      type: Number,
      required: true,
    },
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
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.timesheetId)
      .then(response => {
        const {data, meta} = response.data;
        this.timesheet = {data, meta};
        myTimesheetModal.data = data;
        myTimesheetModal.meta = meta;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onClickReset() {
      this.timesheet = {...myTimesheetModal};
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-timeperiod-title {
  font-size: 12px;
  margin-right: 10px;
}
.orangehrm-form-hint {
  margin-right: auto;
  font-weight: 600;
  font-size: 0.75rem;
  text-overflow: ellipsis;
  overflow: hidden;
}
</style>
