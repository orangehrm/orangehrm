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
    icon="pie-chart-fill"
    :loading="isLoading"
    :title="$t('dashboard.employee_distribution_by_location')"
  >
    <oxd-pie-chart
      :data="dataset"
      :aspect-ratio="false"
      :custom-legend="true"
      :custom-tooltip="true"
      wrapper-classes="emp-distrib-chart"
    ></oxd-pie-chart>
  </base-widget>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';
import {OxdPieChart, CHART_COLORS} from '@ohrm/oxd';

export default {
  name: 'EmployeeLocationWidget',

  components: {
    'base-widget': BaseWidget,
    'oxd-pie-chart': OxdPieChart,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/dashboard/employees/locations',
    );

    return {
      http,
    };
  },

  data() {
    return {
      dataset: [],
      isLoading: false,
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data, meta} = response.data;

        const colors = [
          CHART_COLORS.COLOR_HEAT_WAVE,
          CHART_COLORS.COLOR_CHROME_YELLOW,
          CHART_COLORS.COLOR_YELLOW_GREEN,
          CHART_COLORS.COLOR_MOUNTAIN_MEADOW,
          CHART_COLORS.COLOR_PACIFIC_BLUE,
          CHART_COLORS.COLOR_BLEU_DE_FRANCE,
          CHART_COLORS.COLOR_MAJORELLE_BLUE,
          CHART_COLORS.COLOR_MEDIUM_ORCHID,
          CHART_COLORS.COLOR_FANDANGO_PINK,
        ];

        this.dataset = data
          .map((item, index) => {
            return item.count
              ? {
                  value: item.count,
                  color: colors[index],
                  label: item.location.name,
                }
              : false;
          })
          .filter((item) => item);

        if (meta?.otherEmployeeCount) {
          this.dataset.push({
            value: meta.otherEmployeeCount,
            color: CHART_COLORS.COLOR_FANDANGO_PINK,
            label: this.$t('pim.other'),
          });
        }

        if (meta?.unassignedEmployeeCount) {
          this.dataset.push({
            value: meta.unassignedEmployeeCount,
            color: CHART_COLORS.COLOR_TART_ORANGE,
            label: this.$t('dashboard.unassigned'),
          });
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style lang="scss" scoped>
.emp-distrib-chart {
  width: auto;
  height: 312px;
}
</style>
