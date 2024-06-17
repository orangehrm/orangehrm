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
    <slot :generate-report="generateReport"></slot>
    <div v-if="headers.length !== 0" class="orangehrm-paper-container">
      <oxd-report-table
        :items="items"
        :headers="headers"
        :loading="isLoading"
        :column-count="colCount"
        :can-focus="canFocus"
        :range="range"
      >
        <template #pagination>
          <oxd-text class="oxd-text--count" tag="span">
            {{ $t('general.n_records_found', {count: total}) }}
          </oxd-text>
          <oxd-pagination
            v-if="showPaginator"
            v-model:current="currentPage"
            :length="pages"
          />
        </template>
        <template #footer>
          <oxd-text class="oxd-text--footer" tag="span">
            <slot name="footer" :data="response"></slot>
          </oxd-text>
        </template>
      </oxd-report-table>
    </div>
  </div>
</template>

<script>
import {computed, onBeforeMount, ref, watch} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {CellAdapter, OxdMultilineCell, OxdReportTable} from '@ohrm/oxd';

export default {
  name: 'ReportsTable',

  components: {
    'oxd-report-table': OxdReportTable,
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
      default: null,
      required: false,
    },
    canFocus: {
      type: Boolean,
      default: false,
      required: false,
    },
    range: {
      type: Boolean,
      default: false,
      required: false,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/${props.module}/reports/data`,
    );

    const headers = ref([]);
    const colCount = ref(props.columnCount ? props.columnCount : 0);
    const serializedFilters = computed(() => {
      return {...props.filters, name: props.name, _dateFormattingEnabled: true};
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

    const items = computed(() => {
      const _items = Array.isArray(response.value.data)
        ? response.value.data
        : [];
      return _items.map((item) => {
        let _rows = 0;
        for (const key in item) {
          const value = item[key];
          if (Array.isArray(value) && value.length > _rows)
            _rows = value.length;
        }
        return {...item, _rows};
      });
    });

    const setupTableHeaders = (header) => {
      delete header['size'];
      const {type, ...rest} = header.cellProperties ?? {};
      const cellProperties = function ({prop, model}) {
        const url = model?._url ? model?._url[prop] : undefined;
        return {
          ...rest,
          onClick: url ? () => navigate(url) : undefined,
        };
      };
      return {
        ...header,
        cellProperties,
        cellTemplate:
          type === 'list' ? CellAdapter(OxdMultilineCell) : undefined,
      };
    };

    const fetchTableHeaders = async () => {
      isLoading.value = true;
      http
        .request({
          method: 'GET',
          url: `/api/v2/${props.module}/reports`,
          params: {
            name: serializedFilters.value.name,
            reportId: serializedFilters.value?.reportId,
          },
        })
        .then((response) => {
          const {data, meta} = response.data;
          headers.value = data.headers.map((header) => {
            if (header?.children && Array.isArray(header.children)) {
              header.children = header.children.map((child) =>
                setupTableHeaders(child),
              );
              return header;
            } else {
              return setupTableHeaders(header);
            }
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

    props.prefetch && onBeforeMount(() => generateReport());

    return {
      total,
      pages,
      items,
      headers,
      colCount,
      response,
      isLoading,
      currentPage,
      showPaginator,
      generateReport,
    };
  },
};
</script>

<style src="./reports-table.scss" lang="scss" scoped></style>
