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
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('performance.my_performance_trackers') }}
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
import {computed} from 'vue';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';

const defaultSortOrder = {
  'performanceTracker.trackerName': 'DEFAULT',
  'performanceTracker.addedDate': 'DEFAULT',
  'performanceTracker.modifiedDate': 'DESC',
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
      '/api/v2/performance/trackers',
    );
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const trackerNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          tracker: item.trackerName,
          addedDate: formatDate(parseDate(item.addedDate), jsDateFormat, {
            locale,
          }),
          modifiedDate: formatDate(parseDate(item.modifiedDate), jsDateFormat, {
            locale,
          }),
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
          name: 'tracker',
          slot: 'title',
          title: this.$t('performance.tracker'),
          sortField: 'performanceTracker.trackerName',
          style: {flex: '30%'},
        },
        {
          name: 'addedDate',
          title: this.$t('performance.added_date'),
          sortField: 'performanceTracker.addedDate',
          style: {flex: 1},
        },
        {
          name: 'modifiedDate',
          title: this.$t('performance.modified_date'),
          sortField: 'performanceTracker.modifiedDate',
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

  methods: {
    onClickView(item) {
      navigate('/performance/addPerformanceTrackerLog/trackId/{id}?mode=my', {
        id: item.id,
      });
    },
  },
};
</script>
