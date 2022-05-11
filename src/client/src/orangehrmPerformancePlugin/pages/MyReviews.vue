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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('general.my_reviews') }}
        </oxd-text>
      </div>
      <table-header :selected="0" :total="total" :loading="isLoading">
      </table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
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
  </div>
</template>
<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';
import {computed} from 'vue';
import useDateFormat from "@/core/util/composable/useDateFormat";
import useLocale from "@/core/util/composable/useLocale";
import {formatDate, parseDate} from "@/core/util/helper/datefns";

const defaultSortOrder = {
  'performanceReview.statusId': 'ASC',
  'performanceReview.dueDate': 'ASC',
  'performanceReview.reviewPeriod': 'DEFAULT',
};

export default {
  setup() {
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilter = computed(() => {
      return {
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/myReview',
    );

    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const trackerNormalizer = data => {
      return data.map(item => {
        return {
          id: item.id,
          jobTitle: item.jobTitle.name,
          department: item.department.name,
          reviewPeriod: formatDate(parseDate(item.workPeriodStart), jsDateFormat, {locale}) + ' - ' + formatDate(parseDate(item.workPeriodEnd), jsDateFormat, {locale}),
          dueDate: formatDate(parseDate(item.dueDate), jsDateFormat, {locale}),
          status: item.status,
        };
      });
    };

    const {
      currentPage,
      total,
      showPaginator,
      pages,
      pageSize,
      response,
      execQuery,
      isLoading,
    } = usePaginate(http, {
      query: serializedFilter,
      normalizer: trackerNormalizer,
    });

    onSort(execQuery);

    return {
      http,
      total,
      isLoading,
      items: response,
      execQuery,
      sortDefinition,
      showPaginator,
      pages,
      pageSize,
      currentPage,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'jobTitle',
          slot: 'title',
          title: this.$t('general.job_title'),
          style: {flex: 1},
        },
        {
          name: 'department',
          title: this.$t('general.department'),
          style: {flex: 1},
        },
        {
          name: 'reviewPeriod',
          title: this.$t('performance.review_period'),
          sortField: 'performanceReview.reviewPeriod',
          style: {flex: 2},
        },
        {
          name: 'dueDate',
          title: this.$t('performance.due_date'),
          sortField: 'performanceReview.dueDate',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          sortField: 'performanceReview.statusId',
          style: {flex: 1},
        },
        {
          name: 'action',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                name: 'view',
                label: this.$t('general.view'),
                displayType: 'text',
              },
            },
          },
        },
      ],
    };
  },
};
</script>
