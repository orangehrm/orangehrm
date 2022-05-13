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
  <div class="orangehrm-card-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('recruitment.candidate') }} {{ $t('recruitment.history') }}
      </oxd-text>
    </div>
    <table-header
      :selected="checkedItems.length"
      :total="total"
      :loading="isLoading"
    ></table-header>
    <div class="orangehrm-container">
      <oxd-card-table
        v-model:selected="checkedItems"
        :headers="headers"
        :items="items?.data"
        :clickable="false"
        :loading="isLoading"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div v-if="showPaginator" class="orangehrm-bottom-container">
      <oxd-pagination v-model:current="currentPage" :length="pages" />
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useLocale from '@/core/util/composable/useLocale';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import {navigate} from '@/core/util/helper/navigation';
export default {
  name: 'HistoryTable',
  props: {
    candidateId: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();

    const http = new APIService(
      'https://c81c3149-4936-41d9-ab3d-e25f1bff2934.mock.pstmn.io',
      `/recruitment/candidateHistory/${props.candidateId}`,
    );
    const historyDataNormalizer = data => {
      return data.map(item => {
        return {
          ...item,
          performedDate: formatDate(
            parseDate(item.performedDate),
            jsDateFormat,
            {
              locale,
            },
          ),
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
    } = usePaginate(http, {normalizer: historyDataNormalizer});

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
          name: 'performedDate',
          slot: 'title',
          title: 'PerformedDate',
          style: {flex: 1},
        },
        {name: 'description', title: 'Description', style: {flex: 1}},
        {
          name: 'Actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
        },
      ],
      checkedItems: [],
    };
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};
      if (row.action?.id) {
        cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
      }
      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    onClickEdit(item) {
      navigate(
        `/recruitment/jobInterview/history/${item.action.id}/interviewer/${item.action.interviewId}`,
      );
    },
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-card-container {
  padding: 1.2rem 0;
}
</style>
