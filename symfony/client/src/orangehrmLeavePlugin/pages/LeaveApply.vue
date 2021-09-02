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
            <!-- <oxd-grid-item>
              <leave-balance
                :type="leave.type"
                :fromDate="leave.fromDate"
                :toDate="leave.toDate"
              ></leave-balance>
            </oxd-grid-item> -->
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
        <oxd-form-row v-if="appliedLeaveDuration == 1">
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <leave-duration-input
              :label="$t('general.duration')"
              v-model:duration="leave.duration.type"
              v-model:fromTime="leave.duration.fromTime"
              v-model:toTime="leave.duration.toTime"
            ></leave-duration-input>
          </oxd-grid>
        </oxd-form-row>
        <!-- Single Day|Duration -->

        <!-- Partial Day|Duration -->
        <oxd-form-row v-if="appliedLeaveDuration > 1">
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('leave.partial_days')"
                :options="partialOptions"
                v-model="leave.partialOptions"
              />
            </oxd-grid-item>
            <leave-duration-input
              :partial="true"
              :label="$t('general.duration')"
              v-if="showDuration"
              v-model:duration="leave.duration.type"
              v-model:fromTime="leave.duration.fromTime"
              v-model:toTime="leave.duration.toTime"
            ></leave-duration-input>
            <leave-duration-input
              :partial="true"
              :label="$t('leave.start_day')"
              v-if="showStartDay"
              v-model:duration="leave.duration.type"
              v-model:fromTime="leave.duration.fromTime"
              v-model:toTime="leave.duration.toTime"
            ></leave-duration-input>
            <leave-duration-input
              :partial="true"
              :label="$t('leave.end_day')"
              v-if="showEndDay"
              v-model:duration="leave.endDuration.type"
              v-model:fromTime="leave.endDuration.fromTime"
              v-model:toTime="leave.endDuration.toTime"
            ></leave-duration-input>
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
import {
  required,
  validDateFormat,
  shouldNotExceedCharLength,
  endDateShouldBeAfterStartDate,
} from '@/core/util/validation/rules';
import {diffInDays} from '@orangehrm/core/util/helper/datefns';
import {APIService} from '@orangehrm/core/util/services/api.service';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import LeaveDurationInput from '@/orangehrmLeavePlugin/components/LeaveDurationInput';
// import LeaveBalance from '@/orangehrmLeavePlugin/components/LeaveBalance';

const leaveModel = {
  type: null,
  fromDate: null,
  toDate: null,
  comment: '',
  partialOptions: null,
  duration: {
    type: null,
    fromTime: null,
    toTime: null,
  },
  endDuration: {
    type: null,
    fromTime: null,
    toTime: null,
  },
};

export default {
  name: 'leave-apply',

  components: {
    'leave-type-dropdown': LeaveTypeDropdown,
    'leave-duration-input': LeaveDurationInput,
    // 'leave-balance': LeaveBalance,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/leave-requests',
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
            {allowSameDate: true},
          ),
        ],
        comment: [shouldNotExceedCharLength(250)],
      },
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
      this.isLoading = true;
      const payload = {
        leaveTypeId: this.leave.type?.id,
        fromDate: this.leave.fromDate,
        toDate: this.leave.toDate,
        comment: this.leave.comment ? this.leave.comment : null,
        duration: {
          type: this.leave.duration.type?.key,
          fromTime: this.leave.duration.fromTime,
          toTime: this.leave.duration.toTime,
        },
        partialOption: null,
        endDuration: null,
      };
      if (this.leave.partialOptions?.id) {
        payload.partialOptions = this.leave.partialOptions;
        payload.endDuration = {
          type: this.leave.endDuration.type?.key,
          fromTime: this.leave.endDuration.fromTime,
          toTime: this.leave.endDuration.toTime,
        };
      }
      // TODO: Handle leave errors
      this.http.create(payload).then(() => {
        this.$toast.saveSuccess();
        this.leave = {...leaveModel};
        this.isLoading = false;
      });
    },
  },

  computed: {
    appliedLeaveDuration() {
      return diffInDays(this.leave.fromDate, this.leave.toDate);
    },
    showDuration() {
      const id = this.leave.partialOptions?.id;
      return id && id === 1;
    },
    showStartDay() {
      const id = this.leave.partialOptions?.id;
      return id && (id === 2 || id === 4);
    },
    showEndDay() {
      const id = this.leave.partialOptions?.id;
      return id && (id === 3 || id === 4);
    },
  },
};
</script>
