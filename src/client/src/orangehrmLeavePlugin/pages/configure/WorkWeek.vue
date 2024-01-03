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
      <oxd-text class="orangehrm-main-title">
        {{ $t('leave.work_week') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.monday"
                type="select"
                :options="dayTypes"
                :rules="rules.monday"
                :label="$t('general.monday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.tuesday"
                type="select"
                :options="dayTypes"
                :rules="rules.tuesday"
                :label="$t('general.tuesday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.wednesday"
                type="select"
                :options="dayTypes"
                :rules="rules.wednesday"
                :label="$t('general.wednesday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.thursday"
                type="select"
                :options="dayTypes"
                :rules="rules.thursday"
                :label="$t('general.thursday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.friday"
                type="select"
                :options="dayTypes"
                :rules="rules.friday"
                :label="$t('general.friday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.saturday"
                type="select"
                :options="dayTypes"
                :rules="rules.saturday"
                :label="$t('general.saturday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workWeek.sunday"
                type="select"
                :options="dayTypes"
                :rules="rules.sunday"
                :label="$t('general.sunday')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {required} from '@/core/util/validation/rules';

const workWeekModel = {
  monday: null,
  tuesday: null,
  wednesday: null,
  thursday: null,
  friday: null,
  saturday: null,
  sunday: null,
};

export default {
  props: {
    dayTypes: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/workweek',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      workWeek: {...workWeekModel},
      rules: {
        monday: [required],
        tuesday: [required],
        wednesday: [required],
        thursday: [required],
        friday: [required],
        saturday: [required],
        sunday: [required],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
      })
      .then((response) => {
        const {data} = response.data;
        this.workWeek.monday = this.dayTypes.find(
          (dayType) => dayType.id === data.monday,
        );
        this.workWeek.tuesday = this.dayTypes.find(
          (dayType) => dayType.id === data.tuesday,
        );
        this.workWeek.wednesday = this.dayTypes.find(
          (dayType) => dayType.id === data.wednesday,
        );
        this.workWeek.thursday = this.dayTypes.find(
          (dayType) => dayType.id === data.thursday,
        );
        this.workWeek.friday = this.dayTypes.find(
          (dayType) => dayType.id === data.friday,
        );
        this.workWeek.saturday = this.dayTypes.find(
          (dayType) => dayType.id === data.saturday,
        );
        this.workWeek.sunday = this.dayTypes.find(
          (dayType) => dayType.id === data.sunday,
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      // check if workweek contains at least one working day
      const noWorkingDays = Object.values(this.workWeek).find(
        (dayType) => dayType.id !== 8,
      );

      if (noWorkingDays === undefined) {
        return this.$toast.warn({
          title: this.$t('general.warning'),
          message: this.$t('leave.at_least_one_day_should_be_a_working_day'),
        });
      }

      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            monday: this.workWeek.monday.id,
            tuesday: this.workWeek.tuesday.id,
            wednesday: this.workWeek.wednesday.id,
            thursday: this.workWeek.thursday.id,
            friday: this.workWeek.friday.id,
            saturday: this.workWeek.saturday.id,
            sunday: this.workWeek.sunday.id,
          },
        })
        .then(() => {
          this.$toast.saveSuccess();
          this.isLoading = false;
        });
    },
  },
};
</script>
