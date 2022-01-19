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
    <oxd-grid v-if="punchIn.punchedInTime" :cols="2">
      <oxd-grid-item>
        <br />
        <oxd-text type="subtitle-2">
          <b>Punched In Time</b>
          <br />
          {{ punchIn.punchedInTime }}
        </oxd-text>
        <br />
      </oxd-grid-item>
    </oxd-grid>

    <oxd-form-row>
      <oxd-grid :cols="4" class="orangehrm-full-width-grid">
        <!-- Date Selector -->
        <oxd-grid-item>
          <oxd-input-field
            v-model="punchIn.date"
            :label="$t('general.date')"
            type="date"
            :rules="rules.date"
            :disabled="!editable"
          />
        </oxd-grid-item>

        <!-- Time  Selector -->
        <oxd-grid-item>
          <oxd-input-field
            v-model="punchIn.time"
            :label="$t('time.time')"
            type="time"
            :rules="rules.time"
            :disabled="!editable"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>

    <!-- select timezone -->

    <oxd-grid v-if="isAdmin" :cols="4">
      <oxd-grid-item>
        <timezone-dropdown v-model="punchIn.timezone" />
      </oxd-grid-item>
    </oxd-grid>

    <!-- Note input -->
    <oxd-form-row>
      <oxd-grid :cols="2" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-field
            v-model="punchIn.note"
            :label="$t('general.note')"
            type="textarea"
            placeholder="Type here."
            :rules="rules.note"
          />
        </oxd-grid-item>
      </oxd-grid>
    </oxd-form-row>
    <oxd-divider class="orangehrm-horizontal-margin" />
    <oxd-form-actions>
      <submit-button :disabled="punched" :label="!recordId ? 'In' : 'Out'" />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
import {freshDate, formatDate} from '@/core/util/helper/datefns';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';
import TimezoneDropdown from '@/orangehrmAttendancePlugin/components/TimezoneDropdown.vue';

export default {
  name: 'RecordAttendance',
  components: {
    'timezone-dropdown': TimezoneDropdown,
  },
  props: {
    recordId: {
      type: String,
      default: null,
    },
    editable: {
      type: Boolean,
      default: true,
    },
    isAdmin: {
      type: Boolean,
      default: false,
    },
  },

  setup() {
    //date punch-in data submiting Api
    const http = new APIService(
      'https://62fc498d-0f01-41d2-b3ed-bd7280ebc66c.mock.pstmn.io',
      '/api/v2/attendance/records',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      punchIn: {
        date: null,
        time: null,
        note: null,
        punchedInTime: null,
        timezone: null,
      },
      punched: false,
      rules: {
        date: [required, promiseDebounce(this.validateDate, 500)],
        time: [required, promiseDebounce(this.validateDate, 500)],
        note: [shouldNotExceedCharLength(255)],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;

    //get latest time and date from BE
    this.http
      .request({url: '/api/v2/attendance/curruntdate', method: 'GET'})
      .then(res => {
        const {data} = res.data;
        this.punchIn.date = data.date;
        this.punchIn.time = data.time;

        //get last punched in time and date
        return this.recordId
          ? this.http.getAll({date: formatDate(freshDate(), 'yyyy-MM-dd')})
          : null;
      })
      .then(res => {
        if (res) {
          const {data} = res.data;
          this.punchIn.punchedInTime =
            data[0].punchIn.utcDate + ' ' + data[0].punchIn.utcTime;
        }
      })
      .finally(() => (this.isLoading = false));
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        date: this.punchIn.date,
        time: this.punchIn.time,
        timezones:
          this.punchIn.timezone?.offset ?? new Date().getTimezoneOffset(),
        note: this.punchIn.note,
      };

      if (!this.recordId) {
        this.http
          .create(payload)
          .then(() => {
            this.punched = true;
            return this.$toast.saveSuccess();
          })
          .then(() => navigate('/attendance/punchOut'))
          .finally(() => {
            this.isLoading = false;
          });
      } else {
        this.http
          .update(this.recordId, payload)
          .then(() => {
            this.punched = true;
            return this.$toast.updateSuccess();
          })
          .then(() => navigate('/attendance/punchIn'))
          .finally(() => {
            this.isLoading = false;
          });
      }
    },

    //sending time and date to validaton => todo
    validateDate() {
      return new Promise(resolve => {
        this.http
          .request({url: '/api/v2/attendance/validatedate', method: 'GET'})
          .then(res => {
            const {data} = res.data;
            return data ? resolve(true) : resolve('Overlapping Records Found');
          });
      });
    },
  },
};
</script>
