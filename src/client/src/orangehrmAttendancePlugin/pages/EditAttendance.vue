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
          {{ $t('attendance.edit_attendance_records') }}
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
                    {{ $t('attendance.punch_in') }}
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
                    :label="$t('general.time')"
                    :rules="rules.punchIn.userTime"
                    type="time"
                    :placeholder="$t('attendance.hh_mm')"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item
                  v-if="isTimezoneEditable"
                  class="--offset-row-3 --span-column-2"
                >
                  <timezone-dropdown
                    v-model="attendance.punchIn.timezone"
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
                    {{ $t('attendance.punch_out') }}
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
                    :label="$t('general.time')"
                    :rules="rules.punchOut.userTime"
                    type="time"
                    :placeholder="$t('attendance.hh_mm')"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item
                  v-if="isTimezoneEditable"
                  class="--offset-row-3 --span-column-2"
                >
                  <timezone-dropdown
                    v-model="attendance.punchOut.timezone"
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
import {diffInTime, secondsTohhmm} from '@/core/util/helper/datefns';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';
import TimezoneDropdown from '@/orangehrmAttendancePlugin/components/TimezoneDropdown.vue';

const attendanceRecordModal = {
  userDate: null,
  userTime: null,
  utcDate: null,
  utcTime: null,
  note: null,
  timezone: null,
  timezoneOffset: null,
};

export default {
  components: {
    'timezone-dropdown': TimezoneDropdown,
  },
  props: {
    attendanceId: {
      type: Number,
      required: true,
    },
    isEmployeeEdit: {
      type: Boolean,
      default: false,
    },
    isTimezoneEditable: {
      type: Boolean,
      default: false,
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
        employee: null,
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
      const punchInTz =
        this.attendance.punchIn.timezone?._offset ??
        parseFloat(this.attendance.punchIn.timezoneOffset);
      const startTimezone =
        (punchInTz > 0 ? ' +' : ' -') +
        secondsTohhmm(Math.abs(punchInTz) * 3600);

      const endTime = `${this.attendance.punchOut.userDate} ${this.attendance.punchOut.userTime}`;
      const punchOutTz =
        this.attendance.punchOut.timezone?._offset ??
        parseFloat(this.attendance.punchOut.timezoneOffset);
      const endTimezone =
        (punchOutTz > 0 ? ' +' : ' -') +
        secondsTohhmm(Math.abs(punchOutTz) * 3600);

      // yyyy-MM-dd HH:mm xxx <=> 2022-03-07 14:26 +05:30
      return parseFloat(
        diffInTime(
          startTime + startTimezone,
          endTime + endTimezone,
          'yyyy-MM-dd HH:mm xxx',
        ) / 3600,
      ).toFixed(2);
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.attendanceId)
      .then(response => {
        const {data} = response.data;
        this.attendance.employee = data.employee;
        this.attendance.punchIn = {
          ...data.punchIn,
          timezone: {
            id: data.punchIn.timezone.name,
            label: data.punchIn.timezone.label,
            _offset: data.punchIn.timezoneOffset,
          },
        };
        this.attendance.punchOut = data.punchOut?.userDate
          ? {
              ...data.punchOut,
              timezone: {
                id: data.punchOut.timezone.name,
                label: data.punchOut.timezone.label,
                _offset: data.punchOut.timezoneOffset,
              },
            }
          : null;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onCancel() {
      if (this.isEmployeeEdit) {
        navigate('/attendance/viewAttendanceRecord', undefined, {
          employeeId: this.attendance.employee?.empNumber,
          date: this.attendance.punchIn?.userDate,
        });
      } else {
        navigate('/attendance/viewMyAttendanceRecord', undefined, {
          date: this.attendance.punchIn?.userDate,
        });
      }
    },
    onSave() {
      this.isLoading = true;
      const payload = {
        punchInDate: this.attendance.punchIn.userDate,
        punchInTime: this.attendance.punchIn.userTime,
        punchInNote: this.attendance.punchIn.note,
        ...(this.isTimezoneEditable && {
          punchInOffset: this.attendance.punchIn.timezone
            ? this.attendance.punchIn.timezone._offset
            : this.attendance.punchIn.timezoneOffset,
          punchInTimezoneName: this.attendance.punchIn.timezone
            ? this.attendance.punchIn.timezone.id
            : this.attendance.punchIn.timezone.name,
        }),
      };
      if (this.attendance.punchOut) {
        payload.punchOutDate = this.attendance.punchOut.userDate;
        payload.punchOutTime = this.attendance.punchOut.userTime;
        payload.punchOutNote = this.attendance.punchOut.note;
        if (this.isTimezoneEditable) {
          payload.punchOutOffset = this.attendance.punchOut.timezone
            ? this.attendance.punchOut.timezone._offset
            : this.attendance.punchOut.timezoneOffset;
          payload.punchOutTimezoneName = this.attendance.punchOut.timezone
            ? this.attendance.punchOut.timezone.id
            : this.attendance.punchOut.timezone.name;
        }
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
              punchInTimezoneOffset: this.attendance.punchIn.timezone
                ? this.attendance.punchIn.timezone._offset
                : this.attendance.punchIn.timezoneOffset,
              punchInDate: this.attendance.punchIn.userDate,
              punchInTime: this.attendance.punchIn.userTime,
              punchOutTimezoneOffset: this.attendance.punchOut?.timezone
                ? this.attendance.punchOut.timezone._offset
                : this.attendance.punchOut?.timezoneOffset,
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
              : resolve(this.$t('attendance.overlapping_records_found'));
          });
      });
    },
  },
};
</script>

<style src="./edit-attendance.scss" lang="scss" scoped></style>
