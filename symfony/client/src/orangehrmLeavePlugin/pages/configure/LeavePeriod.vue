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
        {{ $t('leave.leave_period') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="leavePeriod.startMonth"
                type="select"
                :options="months"
                :rules="rules.startMonth"
                :label="$t('leave.start_month')"
                required
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                v-model="leavePeriod.startDay"
                type="select"
                :options="dates"
                :rules="rules.startDay"
                :label="$t('general.start_date')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-group :label="$t('general.end_date')">
                <oxd-text type="subtitle-2" class="orangehrm-leave-period">
                  {{ endDay }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>

            <oxd-grid-item v-if="leavePeriod.currentPeriod">
              <oxd-input-group :label="$t('leave.current_leave_period')">
                <oxd-text type="subtitle-2" class="orangehrm-leave-period">
                  {{ leavePeriod.currentPeriod }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.reset')"
            @click="onClickReset"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {reloadPage} from '@ohrm/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';
import {enGB} from 'date-fns/locale';
import {addDays, formatDate} from '@/core/util/helper/datefns';

const leavePeriodModel = {
  startMonth: null,
  startDay: null,
  currentPeriod: null,
};

export default {
  props: {
    monthDates: {
      type: Object,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/leave-period',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      leavePeriod: {...leavePeriodModel},
      leavePeriodDefined: true,
      rules: {
        startMonth: [required],
        startDay: [required],
      },
    };
  },

  computed: {
    months() {
      return Array(12)
        .fill('')
        .map((...[, index]) => {
          return {
            id: index + 1,
            label: enGB.localize.month(index, {
              width: 'wide',
            }),
          };
        });
    },
    dates() {
      return (this.monthDates[this.leavePeriod.startMonth?.id] ?? []).map(
        day => {
          return {
            id: day,
            label: String(day).padStart(2, '0'),
          };
        },
      );
    },
    endDay() {
      const month = this.leavePeriod.startMonth?.id;
      const date = this.leavePeriod.startDay?.id;
      const year = new Date().getFullYear();
      if (month && date) {
        const endDay = addDays(new Date(year, month - 1, date), 364);
        const isFollowingYear = endDay.getFullYear() > year;
        return (
          formatDate(endDay, 'LLLL dd') +
          (isFollowingYear ? ` (${this.$t('leave.following_year')})` : '')
        );
      }
      return '-';
    },
  },

  watch: {
    'leavePeriod.startMonth': function() {
      this.leavePeriod.startDay = this.dates.length > 0 ? this.dates[0] : null;
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
      })
      .then(response => {
        const {data, meta} = response.data;
        this.updateLeavePeriodModel(data);
        this.defineLeavePeriod(meta);
        this.resetLeavePeriod();
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
            startMonth: this.leavePeriod.startMonth?.id,
            startDay: this.leavePeriod.startDay?.id,
          },
        })
        .then(response => {
          const {data, meta} = response.data;
          this.updateLeavePeriodModel(data);
          this.defineLeavePeriod(meta);
          this.resetLeavePeriod();
          this.$toast.saveSuccess();
          this.isLoading = false;
          if (!this.leavePeriodDefined) {
            reloadPage();
          }
        });
    },

    onClickReset() {
      this.resetLeavePeriod();
    },

    resetLeavePeriod() {
      this.leavePeriod.startMonth = leavePeriodModel.startMonth;
      this.$nextTick(() => {
        this.leavePeriod.startDay = leavePeriodModel.startDay;
      });
    },

    updateLeavePeriodModel(data) {
      leavePeriodModel.startMonth = this.months.find(m => {
        return m.id === data.startMonth;
      });
      this.$nextTick(() => {
        leavePeriodModel.startDay = this.dates.find(d => {
          return d.id === data.startDay;
        });
      });
    },

    defineLeavePeriod(meta) {
      if (meta.leavePeriodDefined === true) {
        this.leavePeriodDefined = meta.leavePeriodDefined;
        this.leavePeriod.currentPeriod = `
            ${meta.currentLeavePeriod.startDate}
            ${this.$t('general.to').toLowerCase()}
            ${meta.currentLeavePeriod.endDate}
          `;
      } else {
        this.leavePeriodDefined = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-leave-duration {
  padding: $oxd-input-control-vertical-padding 0rem;
}
</style>
