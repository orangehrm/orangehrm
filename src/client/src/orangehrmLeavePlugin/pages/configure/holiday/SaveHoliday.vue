<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('leave.add_holiday') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="holiday.name"
                :label="$t('general.name')"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="holiday.date"
                :label="$t('general.date')"
                :rules="rules.date"
                :years="yearArray"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="holiday.length"
                type="select"
                :label="$t('leave.full_day_half_day')"
                :options="holidayLengthList"
                :rules="rules.length"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group
                :classes="{wrapper: '--status-grouped-field'}"
                :label="$t('leave.repeats_annually')"
              >
                <oxd-input-field
                  v-model="holiday.recurring"
                  type="radio"
                  :option-label="$t('general.yes')"
                  :value="true"
                />
                <oxd-input-field
                  v-model="holiday.recurring"
                  type="radio"
                  :option-label="$t('general.no')"
                  :value="false"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

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
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@ohrm/core/util/validation/rules';
import {yearRange} from '@ohrm/core/util/helper/year-range';
import useDateFormat from '@/core/util/composable/useDateFormat';

const holidayModel = {
  name: '',
  date: '',
  recurring: false,
  length: {id: 0, label: 'Full Day'},
};

export default {
  props: {
    holidayLengthList: {
      type: Array,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/holidays',
    );
    const {userDateFormat} = useDateFormat();

    return {
      http,
      userDateFormat,
    };
  },

  data() {
    return {
      yearArray: [...yearRange(201)],
      isLoading: false,
      holiday: {...holidayModel},
      rules: {
        name: [required, shouldNotExceedCharLength(200)],
        date: [required, validDateFormat(this.userDateFormat)],
        length: [required],
      },
      errors: [],
    };
  },
  created() {
    this.isLoading = true;
    // Fetch list data for unique test
    const today = new Date();
    const startDate =
      today.getFullYear() -
      100 +
      '-' +
      (today.getMonth() + 1) +
      '-' +
      today.getDate();
    const endDate =
      today.getFullYear() +
      100 +
      '-' +
      (today.getMonth() + 1) +
      '-' +
      today.getDate();
    this.http
      .getAll({fromDate: startDate, toDate: endDate, limit: 0})
      .then((response) => {
        const {data} = response.data;
        this.rules.date.push((v) => {
          const index = data.findIndex((item) => item.date == v);
          return index === -1 || this.$t('general.already_exists');
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          name: this.holiday.name,
          date: this.holiday.date,
          recurring: this.holiday.recurring,
          length: this.holiday.length.id,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.holiday = {...holidayModel};
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/leave/viewHolidayList');
    },
  },
};
</script>

<style src="./holiday.scss" lang="scss" scoped></style>
