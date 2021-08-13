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
      <oxd-text class="orangehrm-main-title">
        {{ $t('leave.work_week') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.monday"
                :label="$t('general.monday')"
                v-model="workWeek.monday"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.tuesday"
                :label="$t('general.tuesday')"
                v-model="workWeek.tuesday"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.wednesday"
                :label="$t('general.wednesday')"
                v-model="workWeek.wednesday"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.thursday"
                :label="$t('general.thursday')"
                v-model="workWeek.thursday"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.friday"
                :label="$t('general.friday')"
                v-model="workWeek.friday"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.saturday"
                :label="$t('general.saturday')"
                v-model="workWeek.saturday"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :options="workWeeks"
                :rules="rules.sunday"
                :label="$t('general.sunday')"
                v-model="workWeek.sunday"
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
import {APIService} from '@orangehrm/core/util/services/api.service';
import {required} from '@/core/util/validation/rules';

const workWeekModel = {
  monday: 0,
  tuesday: 0,
  wednesday: 0,
  thursday: 0,
  friday: 0,
  saturday: 0,
  sunday: 0,
};

export default {
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

  props: {
    workWeeks: {
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

  methods: {
    onSave() {
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

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
      })
      .then(response => {
        const {data} = response.data;
        this.workWeek.monday = this.workWeeks.find(
          item => item.id === data.monday,
        );
        this.workWeek.tuesday = this.workWeeks.find(
          item => item.id === data.tuesday,
        );
        this.workWeek.wednesday = this.workWeeks.find(
          item => item.id === data.wednesday,
        );
        this.workWeek.thursday = this.workWeeks.find(
          item => item.id === data.thursday,
        );
        this.workWeek.friday = this.workWeeks.find(
          item => item.id === data.friday,
        );
        this.workWeek.saturday = this.workWeeks.find(
          item => item.id === data.saturday,
        );
        this.workWeek.sunday = this.workWeeks.find(
          item => item.id === data.sunday,
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
