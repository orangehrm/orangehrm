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
    <slot :generateReport="generateReport"></slot>
    <div v-if="headers.length !== 0" class="orangehrm-paper-container">
      <oxd-report-table
        :items="items"
        :headers="headers"
        :loading="isLoading"
        :column-count="colCount"
      >
        <template v-slot:pagination>
          <oxd-text class="orangehrm-horizontal-margin --count" tag="span">
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
import {computed, onBeforeMount, ref, watch} from 'vue';
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
    name: {
      type: String,
      required: true,
    },
    module: {
      type: String,
      required: true,
    },
    prefetch: {
      type: Boolean,
      default: false,
    },
    filters: {
      type: Object,
      default: () => ({}),
    },
    columnCount: {
      type: Number,
      required: false,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/${props.module}/reports/data`,
    );

    const headers = ref([]);
    const colCount = ref(props.columnCount ? props.columnCount : 0);
    const serializedFilters = computed(() => {
      return {...props.filters, name: props.name};
    });

    const {
      total,
      pages,
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
      return response.value.data ?? [];
    });

    const fetchTableHeaders = async () => {
      isLoading.value = true;
      http
        .request({
          type: 'GET',
          url: `api/v2/${props.module}/reports`,
          params: {
            name: serializedFilters.value.name,
            reportId: serializedFilters.value?.reportId,
          },
        })
        .then(response => {
          const {data, meta} = response.data;
          headers.value = data.headers.map(header => {
            delete header['size'];
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
          if (meta.headers?.columnCount) {
            colCount.value = meta.headers.columnCount;
          }
          isLoading.value = false;
        });
    };

    const generateReport = async () => {
      if (headers.value.length === 0) await fetchTableHeaders();
      await fetchTableData();
    };

    watch(
      () => props.name,
      () => {
        headers.value = [];
      },
    );

    props.prefetch && onBeforeMount(generateReport());

    return {
      pages,
      items,
      headers,
      colCount,
      isLoading,
      currentPage,
      itemCountText,
      showPaginator,
      generateReport,
    };
  },
};
</script>

<style src="./reports-table.scss" lang="scss" scoped></style>
