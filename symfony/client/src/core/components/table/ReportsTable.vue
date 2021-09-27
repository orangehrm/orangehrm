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
    <slot :fetchdata="fetchTableData"></slot>
    <br />
    <div class="orangehrm-paper-container">
      <oxd-report-table
        :height="400"
        :items="items"
        :headers="headers"
        :loading="isLoading"
      >
        <template v-slot:pagination>
          <oxd-text class="orangehrm-horizontal-margin" tag="span">
            {{ itemCountText }}
          </oxd-text>
          <oxd-pagination
            v-if="showPaginator"
            :length="pages"
            v-model:current="currentPage"
          />
        </template>
      </oxd-report-table>
    </div>
  </div>
</template>

<script>
import {computed, onBeforeMount, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import ReportTable from '@orangehrm/oxd/core/components/ReportTable/ReportTable';

export default {
  name: 'reports-table',

  components: {
    'oxd-report-table': ReportTable,
  },

  props: {
    prefetch: {
      type: Boolean,
      default: false,
    },
    filters: {
      type: Object,
      default: () => ({}),
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/reports/data',
    );

    const headers = ref([]);
    const serializedFilters = computed(() => props.filters);

    const {
      total,
      pages,
      pageSize,
      response,
      isLoading,
      currentPage,
      showPaginator,
      execQuery: fetchTableData,
    } = usePaginate(http, {
      query: serializedFilters,
      prefetch: false,
    });

    const itemCountText = computed(() => {
      if (!total.value) return `No Records Found`;
      return total.value === 1
        ? `(${total.value}) Record Found`
        : `(${total.value}) Records Found`;
    });

    const items = computed(() => {
      const data = response.value.data ?? [];
      return data.length >= pageSize.value
        ? data
        : data.concat(Array(pageSize.value - data.length).fill({}));
    });

    const fetchTableHeaders = async () => {
      isLoading.value = true;
      http
        .request({
          type: 'GET',
          url: 'api/v2/leave/reports',
          params: {
            name: serializedFilters.value.name,
          },
        })
        .then(response => {
          const data = response.data.data;
          headers.value = data.headers.map(header => {
            const cellProperties = function({prop, model}) {
              const url = model?._url ? model?._url[prop] : undefined;
              return {
                ...header.cellProperties,
                onClick: url ? () => navigate(url) : undefined,
              };
            };
            return {
              ...header,
              cellProperties,
            };
          });
          isLoading.value = false;
        });
    };

    onBeforeMount(async () => {
      await fetchTableHeaders();
      props.prefetch && (await fetchTableData());
    });

    return {
      pages,
      items,
      headers,
      isLoading,
      currentPage,
      itemCountText,
      showPaginator,
      fetchTableData,
    };
  },
};
</script>
