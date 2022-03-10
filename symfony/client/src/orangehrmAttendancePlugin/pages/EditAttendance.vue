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
      <div class="orangehrm-header">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('time.edit_attendance_records') }}
        </oxd-text>
        <oxd-text
          v-if="totalDuration"
          tag="span"
          class="orangehrm-header-total"
        >
          {{ $t('time.total_duration') }}: {{ totalDuration }}
        </oxd-text>
      </div>
      <oxd-divider />

      <div class="orangehrm-paper-container">
        <oxd-form :loading="isLoading" @submitValid="onSave">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid no-gap">
            <oxd-form-row>
              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-text type="subtitle-2">
                    {{ $t('time.punch_in') }}
                  </oxd-text>
                </oxd-grid-item>

                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="attendance.punchIn.userDate"
                    :label="$t('general.date')"
                    :rules="rules.punchIn.userDate"
                    type="date"
                    :placeholder="$t('general.date_format')"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="attendance.punchIn.userTime"
                    :label="$t('time.time')"
                    :rules="rules.punchIn.userTime"
                    type="time"
                    :placeholder="$t('time.hh_mm')"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--span-column-2">
                  <oxd-input-field
                    v-model="attendance.punchIn.note"
                    :rules="rules.punchIn.note"
                    :label="$t('general.note')"
                    :placeholder="$t('general.type_here')"
                    type="textarea"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>

            <oxd-form-row v-if="attendance.punchOut">
              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-text type="subtitle-2">
                    {{ $t('time.punch_out') }}
                  </oxd-text>
                </oxd-grid-item>

                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="attendance.punchOut.userDate"
                    :label="$t('general.date')"
                    :rules="rules.punchOut.userDate"
                    type="date"
                    :placeholder="$t('general.date_format')"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="attendance.punchOut.userTime"
                    :label="$t('time.time')"
                    :rules="rules.punchOut.userTime"
                    type="time"
                    :placeholder="$t('time.hh_mm')"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--span-column-2">
                  <oxd-input-field
                    v-model="attendance.punchOut.note"
                    :rules="rules.punchOut.note"
                    :label="$t('general.note')"
                    :placeholder="$t('general.type_here')"
                    type="textarea"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
          </oxd-grid>

          <oxd-divider />
          <oxd-form-actions>
            <required-text />
            <oxd-button
              display-type="ghost"
              :label="$t('general.cancel')"
              @click="onCancel"
            />
            <submit-button />
          </oxd-form-actions>
        </oxd-form>
      </div>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import {diffInTime} from '@/core/util/helper/datefns';
import {APIService} from '@/core/util/services/api.service';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';

const attendanceRecordModal = {
  userDate: null,
  userTime: null,
  utcDate: null,
  utcTime: null,
  note: null,
  timezoneOffset: null,
};

export default {
  props: {
    attendanceId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/attendance/records`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      attendance: {
        punchIn: {...attendanceRecordModal},
        punchOut: {...attendanceRecordModal},
      },
      rules: {
        punchIn: {
          userDate: [
            required,
            promiseDebounce(
              () => this.validateRecord('punch-in-overlaps'),
              500,
            ),
          ],
          userTime: [
            required,
            promiseDebounce(
              () => this.validateRecord('punch-in-overlaps'),
              500,
            ),
          ],
          note: [shouldNotExceedCharLength(250)],
        },
        punchOut: {
          userDate: [
            required,
            promiseDebounce(
              () => this.validateRecord('punch-out-overlaps'),
              500,
            ),
          ],
          userTime: [
            required,
            promiseDebounce(
              () => this.validateRecord('punch-out-overlaps'),
              500,
            ),
          ],
          note: [shouldNotExceedCharLength(250)],
        },
      },
    };
  },
  computed: {
    totalDuration() {
      if (!this.attendance.punchOut?.userDate) return null;
      const startTime = `${this.attendance.punchIn.userDate} ${this.attendance.punchIn.userTime}`;
      const endTime = `${this.attendance.punchOut.userDate} ${this.attendance.punchOut.userTime}`;
      return parseFloat(
        diffInTime(startTime, endTime, 'yyyy-MM-dd HH:mm') / 3600,
      ).toFixed(2);
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.attendanceId)
      .then(response => {
        const {data} = response.data;
        this.attendance.punchIn = data.punchIn;
        this.attendance.punchOut = data.punchOut?.userDate
          ? data.punchOut
          : null;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onCancel() {
      navigate('/attendance/viewMyAttendanceRecord', undefined, {
        date: this.attendance.punchIn?.userDate,
      });
    },
    onSave() {
      this.isLoading = true;
      const payload = {
        punchInDate: this.attendance.punchIn.userDate,
        punchInTime: this.attendance.punchIn.userTime,
        punchInNote: this.attendance.punchIn.note,
      };
      if (this.attendance.punchOut) {
        payload.punchOutDate = this.attendance.punchOut.userDate;
        payload.punchOutTime = this.attendance.punchOut.userTime;
        payload.punchOutNote = this.attendance.punchOut.note;
      }
      this.http
        .update(this.attendanceId, payload)
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    validateRecord(apiPath) {
      return new Promise(resolve => {
        this.http
          .request({
            method: 'GET',
            url: `api/v2/attendance/records/${apiPath}`,
            params: {
              recordId: this.attendanceId,
              punchInTimezoneOffset: this.attendance.punchIn.timezoneOffset,
              punchInDate: this.attendance.punchIn.userDate,
              punchInTime: this.attendance.punchIn.userTime,
              punchOutTimezoneOffset: this.attendance.punchOut?.timezoneOffset,
              punchOutDate: this.attendance.punchOut?.userDate,
              punchOutTime: this.attendance.punchOut?.userTime,
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
              : resolve(this.$t('time.overlapping_records_found'));
          });
      });
    },
  },
};
</script>

<style src="./edit-attendance.scss" lang="scss" scoped></style>
