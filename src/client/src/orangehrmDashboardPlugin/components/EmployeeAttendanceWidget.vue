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
  <base-widget
    icon="clock-fill"
    :loading="isLoading"
    :title="$t('dashboard.time_at_work')"
  >
    <div class="orangehrm-attendance-card">
      <div class="orangehrm-attendance-card-profile">
        <div class="orangehrm-attendance-card-profile-image">
          <img
            alt="profile picture"
            class="employee-image"
            :src="`../pim/viewPhoto/empNumber/${empNumber}`"
          />
        </div>
        <div class="orangehrm-attendance-card-profile-record">
          <oxd-text tag="p" class="orangehrm-attendance-card-state">
            {{ lastState }}
          </oxd-text>
          <oxd-text tag="p" class="orangehrm-attendance-card-details">
            {{ lastRecord }}
          </oxd-text>
        </div>
      </div>
      <div class="orangehrm-attendance-card-bar">
        <oxd-text tag="span" class="orangehrm-attendance-card-fulltime">
          <b>{{ dayTotal.hours }}h</b> <b>{{ dayTotal.minutes }}m</b>
          {{ $t('general.today') }}
        </oxd-text>
        <oxd-icon-button
          name="stopwatch"
          display-type="solid-main"
          class="orangehrm-attendance-card-action"
          @click="onClickPunchIn"
        />
      </div>
      <oxd-divider />
      <div class="orangehrm-attendance-card-summary">
        <div class="orangehrm-attendance-card-summary-week">
          <oxd-text tag="p">
            {{ $t('dashboard.this_week') }}
          </oxd-text>
          <oxd-text tag="p">
            {{ currentWeek }}
          </oxd-text>
        </div>
        <div class="orangehrm-attendance-card-summary-total">
          <oxd-icon name="stopwatch" class="orangehrm-attendance-card-icon" />
          <oxd-text tag="p" class="orangehrm-attendance-card-fulltime">
            {{ weekTotal.hours }}h {{ weekTotal.minutes }}m
          </oxd-text>
        </div>
      </div>
    </div>
    <oxd-bar-chart
      :grid="false"
      :data="dataset"
      :y-axsis="false"
      :aspect-ratio="false"
      wrapper-classes="emp-attendance-chart"
    ></oxd-bar-chart>
  </base-widget>
</template>

<script>
import {
  isToday,
  freshDate,
  parseDate,
  formatDate,
} from '@/core/util/helper/datefns';
import {navigate} from '@/core/util/helper/navigation';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';
import {OxdBarChart, OxdIcon, CHART_COLORS} from '@ohrm/oxd';

export default {
  name: 'EmployeeAttendanceWidget',

  components: {
    'oxd-icon': OxdIcon,
    'base-widget': BaseWidget,
    'oxd-bar-chart': OxdBarChart,
  },

  setup() {
    const {locale} = useLocale();
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/dashboard/employees/time-at-work',
    );

    return {
      http,
      locale,
    };
  },

  data() {
    return {
      dataset: [],
      state: null,
      endDate: null,
      userDate: null,
      userTime: null,
      startDate: null,
      isLoading: false,
      empNumber: null,
      timezoneOffset: null,
      dayTotal: {
        hours: 0,
        minutes: 0,
      },
      weekTotal: {
        hours: 0,
        minutes: 0,
      },
    };
  },

  computed: {
    lastState() {
      switch (this.state) {
        case 'PUNCHED IN':
          return this.$t('attendance.punched_in');
        case 'PUNCHED OUT':
          return this.$t('attendance.punched_out');
        default:
          return this.$t('attendance.not_punched_in');
      }
    },
    lastRecord() {
      if (!this.userDate || !this.userTime) return null;
      const parsedDate = parseDate(
        `${this.userDate} ${this.userTime}`,
        'yyyy-MM-dd HH:mm',
      );
      const formattedTime = formatDate(parsedDate, 'hh:mm a', {
        locale: this.locale,
      });

      if (!isToday(parsedDate)) {
        const formattedDate = formatDate(parsedDate, 'MMM do', {
          locale: this.locale,
        });
        return this.$t('dashboard.state_date_at_time_timezone_offset', {
          lastState: this.lastState,
          date: formattedDate,
          time: formattedTime,
          timezoneOffset: this.timezoneOffset,
        });
      }

      return this.$t('dashboard.state_today_at_time_timezone_offset', {
        lastState: this.lastState,
        time: formattedTime,
        timezoneOffset: this.timezoneOffset,
      });
    },
    currentWeek() {
      if (!this.startDate || !this.endDate) return null;
      const startDate = formatDate(parseDate(this.startDate), 'MMM dd', {
        locale: this.locale,
      });
      const endDate = formatDate(parseDate(this.endDate), 'MMM dd', {
        locale: this.locale,
      });
      return `${startDate} - ${endDate}`;
    },
  },

  beforeMount() {
    this.fetchWidgetData();
  },

  methods: {
    onClickPunchIn() {
      navigate('/attendance/punchIn');
    },
    fetchWidgetData() {
      this.isLoading = true;
      const currentDate = freshDate();
      const timezoneOffset = (currentDate.getTimezoneOffset() / 60) * -1;
      this.http
        .getAll({
          timezoneOffset,
          currentDate: formatDate(currentDate, 'yyyy-MM-dd'),
          currentTime: formatDate(new Date(), 'HH:mm'),
        })
        .then((response) => {
          const {data, meta} = response.data;
          this.dataset = data.map((item) => ({
            value: item.totalTime.hours + item.totalTime.minutes / 60,
            label: this.$t(
              `general.${new String(item.workDay.day).toLowerCase()}`,
            ),
            color: CHART_COLORS.COLOR_HEAT_WAVE,
          }));

          const {lastAction, currentDay, currentWeek, currentUser} = meta;
          if (lastAction) {
            this.state = lastAction.state;
            this.userDate = lastAction.userDate;
            this.userTime = lastAction.userTime;
            this.timezoneOffset = lastAction.timezoneOffset;
          }
          if (currentWeek) {
            this.weekTotal = currentWeek.totalTime;
            this.endDate = currentWeek.endDate?.date;
            this.startDate = currentWeek.startDate?.date;
          }
          if (currentDay) {
            this.dayTotal = currentDay.totalTime;
          }
          if (currentUser) {
            this.empNumber = currentUser.empNumber;
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style src="./employee-attendance-widget.scss" lang="scss" scoped></style>
