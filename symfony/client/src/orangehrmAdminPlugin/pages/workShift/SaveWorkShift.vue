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
      <oxd-text tag="h6" class="orangehrm-main-title">Add Work Shift</oxd-text>

      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('admin.shift_name')"
                :rules="rules.name"
                v-model="workShift.name"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title">Working Hours*</oxd-text>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="time"
                :rules="rules.fromTime"
                :label="$t('general.from')"
                v-model="workShift.startTime"
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                type="time"
                :rules="rules.endTime"
                :label="$t('general.to')"
                v-model="workShift.endTime"
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-group :label="$t('admin.duration_per_day')">
                <oxd-text class="orangehrm-workshift-duration" tag="p">
                  {{ selectedTimeDuration }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <work-shift-employee-autocomplete
                v-model="workShift.empNumbers"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button displayType="ghost" label="Cancel" @click="onCancel" />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@orangehrm/core/util/services/api.service';
import {
  endTimeShouldBeAfterStartTime,
  required,
  shouldNotExceedCharLength,
  validTimeFormat,
} from '@orangehrm/core/util/validation/rules';
import {diffInTime} from '@/core/util/helper/datefns';
import WorkShiftEmployeeAutocomplete from '@/orangehrmAdminPlugin/components/WorkShiftEmployeeAutocomplete';

const workShiftModel = {
  id: '',
  name: '',
  hoursPerDay: '',
  startTime: '',
  endTime: '',
  empNumbers: [],
};
export default {
  components: {
    'work-shift-employee-autocomplete': WorkShiftEmployeeAutocomplete,
  },
  props: {
    workShiftConfig: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isLoading: false,
      workShift: {...workShiftModel},
      rules: {
        name: [required, shouldNotExceedCharLength(50)],
        fromTime: [required, validTimeFormat],
        endTime: [
          required,
          validTimeFormat,
          endTimeShouldBeAfterStartTime(
            () => this.workShift.startTime,
            'To time should be after from time',
          ),
        ],
      },
    };
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/work-shifts',
    );
    return {
      http,
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        name: this.workShift.name,
        hoursPerDay: this.selectedTimeDuration,
        startTime: this.workShift.startTime,
        endTime: this.workShift.endTime,
        empNumbers: this.workShift.empNumbers.map(employee => employee.id),
      };
      this.http
        .create(payload)
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.workShift = {...workShiftModel};
          this.isLoading = false;
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/admin/workShift');
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.workShift.startTime = this.workShiftConfig.startTime;
    this.workShift.endTime = this.workShiftConfig.endTime;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name == v);
          return index === -1 || 'Already exists';
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  computed: {
    selectedTimeDuration() {
      return parseFloat(
        diffInTime(this.workShift.startTime, this.workShift.endTime) / 3600,
      ).toFixed(2);
    },
  },
};
</script>
<style src="./work-shift.scss" lang="scss" scoped></style>
