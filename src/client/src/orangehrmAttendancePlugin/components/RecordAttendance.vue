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
        <template v-if="attendanceRecord.previousRecord">
          <oxd-grid-item
            :class="
              !attendanceRecord.previousRecord.note ? '--span-column-2' : ''
            "
          >
            <oxd-input-group :label="$t('attendance.punched_in_time')">
              <oxd-text type="subtitle-2">
                {{ attendanceRecord.previousRecord.userDate }} -
                {{ attendanceRecord.previousRecord.userTime }}
                <oxd-text
                  tag="span"
                  class="orangehrm-attendance-punchedIn-timezone"
                >
                  {{ `(GMT ${previousRecordTimezone})` }}
                </oxd-text>
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>

          <oxd-grid-item v-if="attendanceRecord.previousRecord.note">
            <oxd-input-group :label="$t('attendance.punched_in_note')">
              <oxd-text type="subtitle-2">
                {{ attendanceRecord.previousRecord.note }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
        </template>

        <!-- Date Selector -->
        <oxd-grid-item class="--offset-row-2">
          <oxd-input-field
            :key="attendanceRecord.time"
            v-model="attendanceRecord.date"
            :label="$t('general.date')"
            :rules="rules.date"
            :disabled="!isEditable"
            type="date"
            :placeholder="$t('general.date_format')"
            required
          />
        </oxd-grid-item>

        <!-- Time  Selector -->
        <oxd-grid-item class="--offset-row-2">
          <oxd-input-field
            v-model="attendanceRecord.time"
            :label="$t('general.time')"
            :disabled="!isEditable"
            :rules="rules.time"
            type="time"
            :placeholder="$t('attendance.hh_mm')"
            required
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <!-- select timezone -->

    <oxd-grid v-if="isTimezoneEditable" :cols="2">
      <oxd-grid-item>
        <timezone-dropdown v-model="attendanceRecord.timezone" required />
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
            :placeholder="$t('general.type_here')"
            type="textarea"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-divider />
    <oxd-form-actions>
      <required-text />
      <submit-button
        :label="
          !attendanceRecordId ? $t('attendance.in') : $t('attendance.out')
        "
      />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {
  parseDate,
  formatDate,
  guessTimezone,
  setClockInterval,
  getStandardTimezone,
} from '@/core/util/helper/datefns';
import {reloadPage, navigate} from '@/core/util/helper/navigation';
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
      default: false,
    },
    isTimezoneEditable: {
      type: Boolean,
      default: false,
    },
    attendanceRecordId: {
      type: Number,
      default: null,
    },
    employeeId: {
      type: Number,
      default: null,
    },
    date: {
      type: String,
      default: null,
    },
  },
  setup(props) {
    const apiPath = props.employeeId
      ? `/api/v2/attendance/employees/${props.employeeId}/records`
      : '/api/v2/attendance/records';
    const http = new APIService(window.appGlobal.baseUrl, apiPath);

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
        note: [shouldNotExceedCharLength(250)],
      },
      previousRecordTimezone: null,
    };
  },
  beforeMount() {
    this.isLoading = true;
    // set default timezone
    if (this.isTimezoneEditable) {
      const tz = guessTimezone();
      this.attendanceRecord.timezone = {
        id: tz.name,
        label: tz.label,
        _name: tz.name,
        _offset: tz.offset,
      };
    }

    // fetch and set attendance record on initial load
    this.setCurrentDateTime()
      .then(() => {
        // then set record date/time every minute
        !this.date &&
          !this.isEditable &&
          setClockInterval(this.setCurrentDateTime, 60000);
        let url = '/api/v2/attendance/records/latest';
        if (this.employeeId) {
          url = `/api/v2/attendance/records/latest?empNumber=${this.employeeId}`;
        }
        return this.attendanceRecordId
          ? this.http.request({method: 'GET', url})
          : null;
      })

      .then(response => {
        if (response) {
          const {data} = response.data;
          this.attendanceRecord.previousRecord = data.punchIn;
        }
      })
      .then(() => {
        this.previousRecordTimezone = getStandardTimezone(
          this.attendanceRecord.previousRecord?.offset,
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;

      const timezone = guessTimezone();

      this.http
        .request({
          method: this.attendanceRecordId ? 'PUT' : 'POST',
          data: {
            date: this.attendanceRecord.date,
            time: this.attendanceRecord.time,
            note: this.attendanceRecord.note,
            timezoneOffset:
              this.attendanceRecord.timezone?._offset ?? timezone.offset,
            timezoneName: this.attendanceRecord.timezone?.id ?? timezone.name,
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.employeeId
            ? navigate('/attendance/viewAttendanceRecord', undefined, {
                employeeId: this.employeeId,
                date: this.date,
              })
            : reloadPage();
        });
    },
    setCurrentDateTime() {
      return new Promise((resolve, reject) => {
        this.http
          .request({method: 'GET', url: '/api/v2/attendance/current-datetime'})
          .then(res => {
            const {utcDate, utcTime} = res.data.data;
            const currentDate = parseDate(
              `${utcDate} ${utcTime} +00:00`,
              'yyyy-MM-dd HH:mm xxx',
            );
            this.attendanceRecord.date =
              this.date ?? formatDate(currentDate, 'yyyy-MM-dd');
            this.attendanceRecord.time = formatDate(currentDate, 'HH:mm');
            resolve();
          })
          .catch(error => reject(error));
      });
    },
    validateDate() {
      if (!this.attendanceRecord.date || !this.attendanceRecord.time) {
        return true;
      }
      const tzOffset = (new Date().getTimezoneOffset() / 60) * -1;
      return new Promise(resolve => {
        this.http
          .request({
            method: 'GET',
            url: `/api/v2/attendance/${
              this.attendanceRecordId ? 'punch-out' : 'punch-in'
            }/overlaps`,
            params: {
              date: this.attendanceRecord.date,
              time: this.attendanceRecord.time,
              timezoneOffset:
                this.attendanceRecord.timezone?._offset ?? tzOffset,
              empNumber: this.employeeId,
            },
            // Prevent triggering response interceptor on 400
            validateStatus: status => {
              return (status >= 200 && status < 300) || status == 400;
            },
          })
          .then(res => {
            const {data, error} = res.data;
            if (error) {
              return resolve(error.message);
            }
            return data.valid === true
              ? resolve(true)
              : resolve(this.$t('attendance.overlapping_records_found'));
          });
      });
    },
  },
};
</script>

<style src="./record-attendance.scss" lang="scss" scoped></style>
