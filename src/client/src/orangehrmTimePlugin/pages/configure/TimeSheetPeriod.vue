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
        {{ $t('time.timesheet_period_config') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="timeSheetPeriod.startDay"
                type="select"
                :label="$t('time.add_timesheet_period_config')"
                :options="days"
                :rules="rules.startDay"
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
import {enGB} from 'date-fns/locale';
import {required} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';

const timeSheetPeriodModel = {
  startDay: null,
};

export default {
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/time-sheet-period',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      timeSheetPeriod: {...timeSheetPeriodModel},
      rules: {
        startDay: [required],
      },
    };
  },
  computed: {
    days() {
      return Array(7)
        .fill('')
        .map((...[, index]) => {
          return {
            id: index === 0 ? 7 : index,
            label: enGB.localize.day(index, {
              width: 'wide',
            }),
          };
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
        this.updateTimeSheetPeriodModel(parseInt(data.startDay));
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            startDay: this.timeSheetPeriod.startDay?.id,
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate('/time/viewEmployeeTimesheet');
        });
    },
    updateTimeSheetPeriodModel(day) {
      this.timeSheetPeriod.startDay = this.days.find(d => {
        return d.id === day;
      });
    },
  },
};
</script>
