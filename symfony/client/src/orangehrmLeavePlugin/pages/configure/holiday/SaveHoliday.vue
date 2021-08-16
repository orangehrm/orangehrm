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
        {{ $t('leave.add_holiday') }}
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
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('leave.full_day_half_day')"
                v-model="holiday.length"
                :options="holidayLengthList"
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

  data() {
    return {
      isLoading: false,
      holiday: {...holidayModel},
      rules: {
        name: [required, shouldNotExceedCharLength(200)],
        date: [required, validDateFormat()],
      },
      errors: [],
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
