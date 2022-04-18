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
  <div v-if="total > 0" class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.actions_performed_on_the_timesheet') }}
      </oxd-text>
    </div>
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
  <div v-else></div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@/core/util/services/api.service';

const actionsNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      action: item.action?.label,
      date: item.date,
      comment: item.comment,
      performedBy: `${
        item.performedEmployee?.firstName
          ? item.performedEmployee?.firstName
          : 'Purged'
      }
      ${
        item.performedEmployee?.lastName
          ? item.performedEmployee?.lastName
          : 'Employee'
      }
       ${item.performedEmployee.terminationId ? ' (Past Employee)' : ''}`,
    };
  });
};

export default {
  name: 'TimesheetActions',

  props: {
    timesheetId: {
      type: Number,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/time/timesheets/${props.timesheetId}/action-logs`,
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
      toastNoRecords: false,
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
          name: 'action',
          slot: 'title',
          title: this.$t('general.actions'),
          style: {flex: 1},
        },
        {
          name: 'performedBy',
          title: this.$t('general.performed_by'),
          style: {flex: 1},
        },
        {
          name: 'date',
          title: this.$t('general.date'),
          style: {flex: 1},
        },
        {
          name: 'comment',
          title: this.$t('general.comment'),
          style: {flex: 1},
        },
      ],
    };
  },
};
</script>
