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
import usei18n from '@/core/util/composable/usei18n';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

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
    const {$t} = usei18n();
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const actionsNormalizer = (data) => {
      return data.map((item) => {
        let performedEmployee;
        let actionName = item.action?.label;
        const firstName = item.performedEmployee?.firstName;
        const lastName = item.performedEmployee?.lastName;

        switch (actionName) {
          case 'Submitted':
            actionName = $t('time.submitted');
            break;
          case 'Rejected':
            actionName = $t('leave.rejected');
            break;
          case 'Not Submitted':
            actionName = $t('time.not_submitted');
            break;
          case 'Approved':
            actionName = $t('time.approved');
            break;
        }

        if (firstName && lastName) {
          performedEmployee = `${firstName} ${lastName}`;
        } else {
          performedEmployee = $t('general.purged_employee');
        }

        if (item.performedEmployee?.terminationId) {
          performedEmployee += ` (${$t('general.past_employee')})`;
        }

        return {
          id: item.id,
          action: actionName,
          date: formatDate(parseDate(item.date), jsDateFormat, {locale}),
          comment: item.comment,
          performedBy: performedEmployee,
        };
      });
    };

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
          cellType: 'oxd-table-cell-truncate',
          title: this.$t('general.comment'),
          style: {flex: 1},
        },
      ],
    };
  },
};
</script>
