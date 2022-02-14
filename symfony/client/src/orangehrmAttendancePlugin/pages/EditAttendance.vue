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
                    v-model="attendance.punchIn.date"
                    :label="$t('general.date')"
                    :rules="rules.punchIn.date"
                    type="date"
                    placeholder="yyyy-mm-dd"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="attendance.punchIn.time"
                    :label="$t('time.time')"
                    :rules="rules.punchIn.time"
                    type="time"
                    placeholder="HH:MM"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--span-column-2">
                  <oxd-input-field
                    v-model="attendance.punchIn.note"
                    :rules="rules.punchIn.note"
                    :label="$t('general.note')"
                    placeholder="Type here."
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
                    v-model="attendance.punchOut.date"
                    :label="$t('general.date')"
                    :rules="rules.punchIn.date"
                    type="date"
                    placeholder="yyyy-mm-dd"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="attendance.punchOut.time"
                    :label="$t('time.time')"
                    :rules="rules.punchIn.time"
                    type="time"
                    placeholder="HH:MM"
                    required
                  />
                </oxd-grid-item>

                <oxd-grid-item class="--span-column-2">
                  <oxd-input-field
                    v-model="attendance.punchOut.note"
                    :rules="rules.punchIn.note"
                    :label="$t('general.note')"
                    placeholder="Type here."
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

const attendanceRecordModal = {
  date: null,
  time: null,
  note: null,
  timezone: null,
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
      `api/v2/attendance/employees/record`,
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
          date: [required],
          time: [required],
          note: [shouldNotExceedCharLength(250)],
        },
        punchOut: {
          date: [required],
          time: [required],
          note: [shouldNotExceedCharLength(250)],
        },
      },
    };
  },
  computed: {
    totalDuration() {
      if (!this.attendance.punchOut?.date) return null;
      const startTime = `${this.attendance.punchIn.date} ${this.attendance.punchIn.time}`;
      const endTime = `${this.attendance.punchOut.date} ${this.attendance.punchOut.time}`;
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
        this.attendance.punchIn = data.in;
        this.attendance.punchOut = data.out;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onCancel() {
      navigate('/attendance/viewMyAttendanceRecord', undefined, {
        date: this.attendance.punchIn?.date,
      });
    },
    onSave() {
      this.isLoading = true;
      const payload = {
        in: {...this.attendance.punchIn},
      };
      if (this.attendance.punchOut) {
        payload.out = {
          ...this.attendance.punchOut,
        };
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
  },
};
</script>

<style src="./edit-attendance.scss" lang="scss" scoped></style>
