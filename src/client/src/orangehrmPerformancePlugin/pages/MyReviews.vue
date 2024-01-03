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
import {computed, inject} from 'vue';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import {navigate} from '@/core/util/helper/navigation';
import {
  viewIcon,
  evaluateIcon,
  viewLabel,
  evaluateLabel,
} from '@/orangehrmPerformancePlugin/util/composable/useReviewActions';
import ReviewPeriodCell from '@/orangehrmPerformancePlugin/components/ReviewPeriodCell';
import {tableScreenStateKey} from '@ohrm/oxd';

const defaultSortOrder = {
  'performanceReview.statusId': 'ASC',
  'performanceReview.dueDate': 'ASC',
  'performanceReview.reviewPeriodStart': 'DEFAULT',
  'reviewer.status': 'DEFAULT',
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
      '/api/v2/performance/reviews',
    );

    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const trackerNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          jobTitle: item.jobTitle.name,
          department: item.subunit.name,
          reviewPeriod: {
            reviewPeriodStart: formatDate(
              parseDate(item.reviewPeriodStart),
              jsDateFormat,
              {
                locale,
              },
            ),
            reviewPeriodEnd: formatDate(
              parseDate(item.reviewPeriodEnd),
              jsDateFormat,
              {
                locale,
              },
            ),
          },
          dueDate: formatDate(parseDate(item.dueDate), jsDateFormat, {locale}),
          overallStatus: item.overallStatus.statusName,
          selfEvaluationStatus: item.selfReviewStatus,
          statusId: item.overallStatus.statusId,
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
          title: this.$t('general.sub_unit'),
          style: {flex: 1},
        },
        {
          name: 'reviewPeriod',
          title: this.$t('performance.review_period'),
          sortField: 'performanceReview.reviewPeriodStart',
          style: {flex: 2},
          cellRenderer: this.reviewPeriodCellRenderer,
        },
        {
          name: 'dueDate',
          title: this.$t('performance.due_date'),
          sortField: 'performanceReview.dueDate',
          style: {flex: 1},
        },
        {
          name: 'selfEvaluationStatus',
          title: this.$t('performance.self_evaluation_status'),
          sortField: 'reviewer.status',
          style: {flex: 1},
        },
        {
          name: 'overallStatus',
          title: this.$t('performance.review_status'),
          sortField: 'performanceReview.statusId',
          style: {flex: 1},
        },
        {
          name: 'action',
          slot: 'footer',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.actionButtonCellRenderer,
        },
      ],
    };
  },
  methods: {
    actionButtonCellRenderer(...[, , , row]) {
      const cellConfig = {};
      const screenState = inject(tableScreenStateKey);
      if (screenState.screenType === 'lg' || screenState.screenType === 'xl') {
        if (row.selfEvaluationStatus === 'Completed') {
          cellConfig.view = viewIcon;
          cellConfig.view.props.title = this.$t('general.view');
          cellConfig.view.onClick = this.onClickEvaluate;
        } else {
          cellConfig.evaluate = evaluateIcon;
          cellConfig.evaluate.props.title = this.$t('performance.evaluate');
          cellConfig.evaluate.onClick = this.onClickEvaluate;
        }
      } else {
        if (row.selfEvaluationStatus === 'Completed') {
          cellConfig.view = viewLabel;
          cellConfig.view.props.label = this.$t('general.view');
          cellConfig.view.onClick = this.onClickEvaluate;
        } else {
          cellConfig.evaluate = evaluateLabel;
          cellConfig.evaluate.props.label = this.$t('performance.evaluate');
          cellConfig.evaluate.onClick = this.onClickEvaluate;
        }
      }

      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    reviewPeriodCellRenderer(...args) {
      const cellData = args[1];
      return {
        component: ReviewPeriodCell,
        props: {
          reviewPeriodStart: cellData.reviewPeriodStart,
          reviewPeriodEnd: cellData.reviewPeriodEnd,
        },
      };
    },
    onClickView() {
      navigate('/performance/searchKpi');
    },
    onClickEvaluate(item) {
      navigate('/performance/reviewEvaluate/id/{id}', {id: item.id});
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
