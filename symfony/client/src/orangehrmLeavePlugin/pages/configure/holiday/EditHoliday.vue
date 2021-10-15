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
        {{ $t('leave.edit_holiday') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('general.name')"
                v-model="holiday.name"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                :label="$t('general.date')"
                v-model="holiday.date"
                :rules="rules.date"
                :years="yearArray"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('leave.full_day_half_day')"
                v-model="holiday.length"
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
                  type="radio"
                  v-model="holiday.recurring"
                  :optionLabel="$t('leave.yes')"
                  :value="true"
                />
                <oxd-input-field
                  type="radio"
                  v-model="holiday.recurring"
                  :optionLabel="$t('leave.no')"
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
            type="button"
            displayType="ghost"
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
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@orangehrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@orangehrm/core/util/validation/rules';
import {yearRange} from '@/core/util/helper/year-range';

const holidayModel = {
  id: '',
  name: '',
  date: '',
  recurring: false,
  length: 0,
};

export default {
  props: {
    holidayId: {
      type: Number,
      required: true,
    },
    holidayLengthList: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      yearArray: [...yearRange(201)],
      isLoading: false,
      holiday: {...holidayModel},
      rules: {
        name: [required, shouldNotExceedCharLength(200)],
        date: [required, validDateFormat()],
        length: [required],
      },
    };
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/holidays',
    );
    return {
      http,
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.holidayId, {
          name: this.holiday.name,
          date: this.holiday.date,
          recurring: this.holiday.recurring,
          length: this.holiday.length.id,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/leave/viewHolidayList');
    },
  },
  created() {
    this.isLoading = true;
    this.http
      .get(this.holidayId)
      .then(response => {
        const {data} = response.data;
        this.holiday.id = data.id;
        this.holiday.name = data.name;
        this.holiday.date = data.date;
        this.holiday.recurring = data.recurring;
        if (data.length !== '' && data.length !== null) {
          this.holiday.length = this.holidayLengthList.find(h => {
            return h.id === data.length;
          });
        }
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
        return this.http.getAll({
          fromDate: startDate,
          toDate: endDate,
          limit: 0,
        });
      })
      .then(response => {
        const {data} = response.data;
        this.rules.date.push(v => {
          const index = data.findIndex(item => item.date === v);
          if (index > -1) {
            const id = data[index].id;
            return id != this.holidayId ? 'Already exists' : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style src="./holiday.scss" lang="scss" scoped></style>
