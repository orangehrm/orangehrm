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
  <oxd-form :loading="isLoading" @submitValid="onSave">
    <oxd-form-row>
      <oxd-grid :cols="4" class="orangehrm-full-width-grid">
        <oxd-grid-item v-if="attendanceRecord.previousRecord">
          <oxd-input-group :label="$t('time.punched_in_time')">
            <oxd-text type="subtitle-2">
              {{ attendanceRecord.previousRecord.date }} -
              {{ attendanceRecord.previousRecord.time }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>

        <!-- Date Selector -->
        <oxd-grid-item class="--offset-row-2">
          <oxd-input-field
            v-model="attendanceRecord.date"
            :label="$t('general.date')"
            :rules="rules.date"
            :disabled="!isEditable"
            type="date"
          />
        </oxd-grid-item>

        <!-- Time  Selector -->
        <oxd-grid-item class="--offset-row-2">
          <oxd-input-field
            v-model="attendanceRecord.time"
            :label="$t('time.time')"
            :disabled="!isEditable"
            :rules="rules.time"
            type="time"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <!-- select timezone -->

    <oxd-grid v-if="isTimezoneEditable" :cols="4">
      <oxd-grid-item>
        <timezone-dropdown v-model="attendanceRecord.timezone" />
      </oxd-grid-item>
    </oxd-grid>

    <!-- Note input -->
    <oxd-form-row>
      <oxd-grid :cols="4" class="orangehrm-full-width-grid">
        <oxd-grid-item class="--span-column-2">
          <oxd-input-field
            v-model="attendanceRecord.note"
            :rules="rules.note"
            :label="$t('general.note')"
            placeholder="Type here."
            type="textarea"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-divider class="orangehrm-horizontal-margin" />
    <oxd-form-actions>
      <submit-button
        :label="!attendanceRecordId ? $t('time.in') : $t('time.out')"
      />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import {setClockInterval, formatDate} from '@/core/util/helper/datefns';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';
import {APIService} from '@ohrm/core/util/services/api.service';
import TimezoneDropdown from '@/orangehrmAttendancePlugin/components/TimezoneDropdown.vue';

const attendanceRecordModal = {
  date: null,
  time: null,
  note: null,
  timezone: null,
  previousRecord: null,
};

export default {
  name: 'RecordAttendance',
  components: {
    'timezone-dropdown': TimezoneDropdown,
  },
  props: {
    isEditable: {
      type: Boolean,
      default: true,
    },
    isTimezoneEditable: {
      type: Boolean,
      default: false,
    },
    attendanceRecordId: {
      type: Number,
      default: null,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/attendance/records',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      attendanceRecord: {...attendanceRecordModal},
      rules: {
        date: [required, promiseDebounce(this.validateDate, 500)],
        time: [required, promiseDebounce(this.validateDate, 500)],
        note: [shouldNotExceedCharLength(255)],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    // fetch and set attendance record on initial load
    this.setCurrentDateTime()
      .then(() => {
        // then set record date/time every minute
        setClockInterval(this.setCurrentDateTime, 60000);
        return this.attendanceRecordId
          ? this.http.get(this.attendanceRecordId)
          : null;
      })
      .then(response => {
        if (response) {
          const {data} = response.data;
          this.attendanceRecord.previousRecord = data;
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        date: this.attendanceRecord.date,
        time: this.attendanceRecord.time,
        timezones:
          this.attendanceRecord.timezone?._offset ??
          new Date().getTimezoneOffset(),
        note: this.attendanceRecord.note,
      };

      if (!this.attendanceRecordId) {
        this.http
          .create(payload)
          .then(() => {
            return this.$toast.saveSuccess();
          })
          .then(() => navigate('/attendance/punchOut'));
      } else {
        this.http
          .update(this.attendanceRecordId, payload)
          .then(() => {
            return this.$toast.updateSuccess();
          })
          .then(() => navigate('/attendance/punchIn'));
      }
    },
    setCurrentDateTime() {
      return new Promise((resolve, reject) => {
        this.http
          .request({method: 'GET', url: '/api/v2/attendance/currentdatetime'})
          .then(res => {
            const {data} = res.data;
            const currentDate = new Date(data.timestamp);
            this.attendanceRecord.date = formatDate(currentDate, 'yyyy-MM-dd');
            this.attendanceRecord.time = formatDate(currentDate, 'HH:mm');
            resolve();
          })
          .catch(error => reject(error));
      });
    },
    validateDate() {
      return new Promise(resolve => {
        this.http
          .request({
            method: 'GET',
            url: '/api/v2/attendance/validatedate',
            params: {
              date: this.attendanceRecord.date,
              time: this.attendanceRecord.time,
            },
          })
          .then(res => {
            const {data} = res.data;
            return data ? resolve(true) : resolve('Overlapping Records Found');
          });
      });
    },
  },
};
</script>
