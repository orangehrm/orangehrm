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
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('leave.apply_leave') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <leave-type-dropdown
                v-model="leave.type"
                :rules="rules.type"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <leave-balance
                :type="leave.type"
                :fromDate="leave.fromDate"
                :toDate="leave.toDate"
              ></leave-balance>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                :label="$t('general.from_date')"
                v-model="leave.fromDate"
                :rules="rules.fromDate"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                :label="$t('general.to_date')"
                v-model="leave.toDate"
                :rules="rules.toDate"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <!-- Single Day|Duration -->
        <oxd-form-row v-if="appliedLeaveDuration <= 1">
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('general.duration')"
                v-model="leave.singleType"
                :rules="rules.singleType"
                :options="singleTypeOptions"
                required
              />
            </oxd-grid-item>
            <leave-duration-input
              :rules="rules"
              :duration="leave.singleType"
              v-model:halfday="leave.singleAMPM"
              v-model:fromTime="leave.singleFromTime"
              v-model:toTime="leave.singleToTime"
            ></leave-duration-input>
          </oxd-grid>
        </oxd-form-row>
        <!-- Single Day|Duration -->

        <!-- Partial Day|Duration -->
        <oxd-form-row v-else>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('leave.partial_days')"
                v-model="leave.partialOption"
                :rules="rules.partialOption"
                :options="partialOptions"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <!-- Partial Day|Duration -->

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="textarea"
                :label="$t('general.comments')"
                v-model="leave.comment"
                :rules="rules.comment"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button :label="$t('general.apply')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {diffInDays} from '@orangehrm/core/util/helper/datefns';
import {APIService} from '@orangehrm/core/util/services/api.service';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import LeaveDurationInput from '@/orangehrmLeavePlugin/components/LeaveDurationInput';
import LeaveBalance from '@/orangehrmLeavePlugin/components/LeaveBalance';
import {
  endDateShouldBeAfterStartDate,
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@/core/util/validation/rules';

const leaveModel = {
  type: null,
  fromDate: null,
  toDate: null,
  comment: '',
  singleType: null,
  singleAMPM: null,
  singleFromTime: null,
  singleToTime: null,
  partialOption: null,
  startDayType: null,
  startDayFromTime: null,
  startDayToTime: null,
  endDayType: null,
  endDayFromTime: null,
  endDayToTime: null,
};

export default {
  name: 'leave-apply',

  components: {
    'leave-type-dropdown': LeaveTypeDropdown,
    'leave-duration-input': LeaveDurationInput,
    'leave-balance': LeaveBalance
  },

  setup() {
    const http = new APIService(
      window.appGlobal.mockUrl,
      'api/v2/leave/my-leave-request',
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      leave: {...leaveModel},
      rules: {
        type: [required],
        fromDate: [required, validDateFormat()],
        toDate: [
          required,
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.leave.fromDate,
            'To date should be after from date',
          ),
        ],
        singleType: [required],
        comment: [shouldNotExceedCharLength(250)],
        fromTime: [required],
        toTime: [required],
        halfday: [required],
        partialOption: [required],
      },
      singleTypeOptions: [
        {id: 1, label: 'Full Day', key: 'full_day'},
        {id: 2, label: 'Half Day', key: 'half_day'},
        {id: 3, label: 'Specify Time', key: 'specify_time'},
      ],
      partialOptions: [
        {id: 1, label: 'All Days'},
        {id: 2, label: 'Start Day Only'},
        {id: 3, label: 'End Day Only'},
        {id: 4, label: 'Start and End Day'},
      ],
    };
  },

  methods: {
    onSave() {
      console.log('here');
    },
  },

  computed: {
    appliedLeaveDuration() {
      return diffInDays(this.leave.fromDate, this.leave.toDate);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-leave-duration {
  padding: 0.675rem 0rem;
}
</style>
