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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.timesheets_pending_action') }}
      </oxd-text>
    </div>
    <table-header
      :selected="0"
      :total="total"
      :loading="isLoading"
    ></table-header>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items?.data"
        :selectable="false"
        :clickable="false"
        :loading="isLoading"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div class="orangehrm-bottom-container">
      <oxd-pagination
        v-if="showPaginator"
        v-model:current="currentPage"
        :length="pages"
      />
    </div>
  </div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';

const actionsNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      startDate: item.startDate,
      empNumber: item.employee.empNumber,
      period: `${item.startDate} - ${item.endDate}`,
      employee: `${item.employee?.firstName} ${item.employee?.lastName} ${
        item.employee?.terminationId ? ' (Past Employee)' : ''
      }`,
    };
  });
};

export default {
  name: 'TimesheetPendingActions',

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/employees/timesheets/list',
    );

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {
      normalizer: actionsNormalizer,
    });

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
    };
  },
  data() {
    return {
      headers: [
        {
          name: 'employee',
          slot: 'title',
          title: this.$t('general.employee_name'),
          style: {flex: '40%'},
        },
        {
          name: 'period',
          title: this.$t('time.timesheet_period'),
          style: {flex: '40%'},
        },
        {
          name: 'actions',
          slot: 'footer',
          title: this.$t('general.actions'),
          style: {flex: '20%'},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                label: this.$t('general.view'),
                displayType: 'text',
                size: 'medium',
              },
            },
          },
        },
      ],
    };
  },

  methods: {
    onClickView(item) {
      navigate(
        '/time/viewTimesheet/employeeId/{empNumber}',
        {empNumber: item.empNumber},
        {startDate: item.startDate},
      );
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
  .oxd-table-cell-actions > * {
    margin: 0 !important;
  }
}
</style>
