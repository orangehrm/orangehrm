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
    <leave-conflict
      v-if="showLeaveConflict"
      :workshift-exceeded="isWorkShiftExceeded"
      :data="leaveConflictData"
    ></leave-conflict>
    <leave-assign-confirm-modal ref="confirmDialog">
    </leave-assign-confirm-modal>
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('leave.assign_leave') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form ref="formRef" :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="leave.employee"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <leave-type-dropdown
                v-model="leave.type"
                :rules="rules.type"
                :eligible-only="false"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <leave-balance :leave-data="leave"></leave-balance>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                v-model="leave.fromDate"
                :label="$t('general.from_date')"
                :rules="rules.fromDate"
                :years="yearsArray"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="leave.toDate"
                :label="$t('general.to_date')"
                :rules="rules.toDate"
                :years="yearsArray"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <!-- Single Day|Duration -->
        <oxd-form-row v-if="appliedLeaveDuration == 1">
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <leave-duration-input
              v-model:duration="leave.duration.type"
              v-model:fromTime="leave.duration.fromTime"
              v-model:toTime="leave.duration.toTime"
              :label="$t('general.duration')"
              :work-shift="workShift"
            ></leave-duration-input>
          </oxd-grid>
        </oxd-form-row>
        <!-- Single Day|Duration -->

        <!-- Partial Day|Duration -->
        <oxd-form-row v-if="appliedLeaveDuration > 1">
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="leave.partialOptions"
                type="select"
                :label="$t('leave.partial_days')"
                :options="partialOptions"
              />
            </oxd-grid-item>
            <leave-duration-input
              v-if="showDuration"
              v-model:duration="leave.duration.type"
              v-model:fromTime="leave.duration.fromTime"
              v-model:toTime="leave.duration.toTime"
              :partial="true"
              :label="$t('general.duration')"
              :work-shift="workShift"
            ></leave-duration-input>
            <leave-duration-input
              v-if="showStartDay"
              v-model:duration="leave.duration.type"
              v-model:fromTime="leave.duration.fromTime"
              v-model:toTime="leave.duration.toTime"
              :partial="true"
              :label="$t('leave.start_day')"
              :work-shift="workShift"
            ></leave-duration-input>
            <leave-duration-input
              v-if="showEndDay"
              v-model:duration="leave.endDuration.type"
              v-model:fromTime="leave.endDuration.fromTime"
              v-model:toTime="leave.endDuration.toTime"
              :partial="true"
              :label="$t('leave.end_day')"
              :work-shift="workShift"
            ></leave-duration-input>
          </oxd-grid>
        </oxd-form-row>
        <!-- Partial Day|Duration -->

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="leave.comment"
                type="textarea"
                :label="$t('general.comments')"
                :rules="rules.comment"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button :label="$t('leave.assign')" />
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
import {yearRange} from '@ohrm/core/util/helper/year-range';
import {diffInDays} from '@ohrm/core/util/helper/datefns';
import {APIService} from '@ohrm/core/util/services/api.service';
import LeaveTypeDropdown from '@/orangehrmLeavePlugin/components/LeaveTypeDropdown';
import LeaveDurationInput from '@/orangehrmLeavePlugin/components/LeaveDurationInput';
import LeaveBalance from '@/orangehrmLeavePlugin/components/LeaveBalance';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import LeaveConflict from '@/orangehrmLeavePlugin/components/LeaveConflict';
import LeaveAssignConfirmModal from '@/orangehrmLeavePlugin/components/LeaveAssignConfirmModal';
import useLeaveValidators from '@/orangehrmLeavePlugin/util/composable/useLeaveValidators';
import useForm from '@ohrm/core/util/composable/useForm';

const leaveModel = {
  employee: null,
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

const defaultWorkshift = {
  startTime: '9:00',
  endTime: '17:00',
};

export default {
  name: 'LeaveAssign',

  components: {
    'leave-type-dropdown': LeaveTypeDropdown,
    'leave-duration-input': LeaveDurationInput,
    'leave-balance': LeaveBalance,
    'employee-autocomplete': EmployeeAutocomplete,
    'leave-conflict': LeaveConflict,
    'leave-assign-confirm-modal': LeaveAssignConfirmModal,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/employees/leave-requests',
    );
    const {
      serializeBody,
      validateLeaveBalance,
      validateOverlapLeaves,
    } = useLeaveValidators(http);
    const {formRef, reset} = useForm();
    return {
      http,
      reset,
      formRef,
      serializeBody,
      validateLeaveBalance,
      validateOverlapLeaves,
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
            this.$t('general.to_date_should_be_after_from_date'),
            {allowSameDate: true},
          ),
        ],
        comment: [shouldNotExceedCharLength(250)],
        employee: [required],
      },
      partialOptions: [
        {id: 1, label: this.$t('leave.all_days'), key: 'all'},
        {id: 2, label: this.$t('leave.start_day_only'), key: 'start'},
        {id: 3, label: this.$t('leave.end_day_only'), key: 'end'},
        {id: 4, label: this.$t('leave.start_and_end_day'), key: 'start_end'},
      ],
      showLeaveConflict: false,
      isWorkShiftExceeded: false,
      leaveConflictData: null,
      yearsArray: [...yearRange()],
      workShift: {...defaultWorkshift},
    };
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

  watch: {
    'leave.employee': function(employee) {
      if (employee) {
        this.http
          .request({
            method: 'GET',
            url: `api/v2/pim/employees/${employee.id}/work-shift`,
          })
          .then(response => {
            const {data} = response.data;
            this.workShift = data;
          });
      } else {
        this.workShift = {...defaultWorkshift};
      }
    },
    appliedLeaveDuration: function(duration) {
      if (duration === 1) {
        this.leave.duration.type = {id: 1, label: 'Full Day', key: 'full_day'};
      } else {
        this.leave.duration.type = null;
      }
    },
    'leave.fromDate': function(fromDate) {
      if (!fromDate || this.leave.toDate) return;
      this.leave.toDate = fromDate;
    },
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.leaveConflictData = null;
      this.showLeaveConflict = false;

      this.validateLeaveBalance(this.leave)
        .then(async ({balance}) => {
          if (balance <= 0) {
            const confirmation = await this.$refs.confirmDialog.showDialog();
            if (confirmation !== 'ok') {
              return Promise.reject();
            }
          }
          return this.validateOverlapLeaves(this.leave);
        })
        .then(({isConflict, isOverWorkshift, data}) => {
          if (isConflict) {
            this.leaveConflictData = data;
            this.showLeaveConflict = true;
            this.isWorkShiftExceeded = isOverWorkshift;
            return Promise.reject();
          }
          return this.http.create(this.serializeBody(this.leave));
        })
        .then(() => {
          this.$toast.saveSuccess();
          this.reset();
        })
        .catch(() => {
          this.showLeaveConflict &&
            this.$toast.warn({
              title: this.$t('general.warning'),
              message: this.$t('leave.failed_to_submit'),
            });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>
