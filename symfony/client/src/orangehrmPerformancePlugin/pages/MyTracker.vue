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
          My Performance Trackers List
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

const defaultSortOrder = {
  trackers: 'DEFAULT',
  date: 'DEFAULT',
  modifiedDate: 'DESC',
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
      'https://990db1a5-734a-4bd6-a39b-12f29fbedfc3.mock.pstmn.io',
      '/api/v2/leave/myTracker',
    );

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
          name: 'trackers',
          slot: 'title',
          title: 'Tracker',
          sortField: 'trackers',
          style: {flex: '30%'},
        },
        {
          name: 'date',
          slot: 'title',
          title: 'Added Date',
          sortField: 'date',
          style: {flex: 1},
        },
        {
          name: 'modifiedDate',
          slot: 'title',
          title: 'Modified Date',
          sortField: 'modifiedDate',
          style: {flex: 1},
        },
        {
          name: 'action',
          slot: 'action',
          title: 'Action',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                name: 'view',
                label: 'View',
                class: 'orangehrm-left-space',
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
