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
  <base-widget
    icon="pie-chart-fill"
    :loading="isLoading"
    :title="$t('general.employee_distribution_by_location')"
  >
    <oxd-pie-chart
      :data="dataset"
      :aspect-ratio="false"
      :custom-legend="true"
      wrapper-classes="emp-distrib-chart"
    ></oxd-pie-chart>
  </base-widget>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {CHART_COLORS} from '@ohrm/oxd/core/components/Chart/types';
import PieChart from '@ohrm/oxd/core/components/Chart/PieChart.vue';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';

export default {
  name: 'EmployeeLocationWidget',

  components: {
    'base-widget': BaseWidget,
    'oxd-pie-chart': PieChart,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/dashboard/employees/location',
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
      .then(response => {
        const {data} = response.data;
        const colors = Object.values(CHART_COLORS).reverse();
        this.dataset = data.map((item, index) => {
          return {
            label: item.label,
            value: item.employeeCount,
            color:
              //TODO: check lang string
              item.label === 'Unassigned'
                ? CHART_COLORS.COLOR_TART_ORANGE
                : colors[index],
          };
        });
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
  height: 330px;
}
</style>
