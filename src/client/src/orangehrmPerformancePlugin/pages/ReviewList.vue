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
    <oxd-table-filter :filter-title="$t('performance.employee_reviews')">
      <oxd-form @submit-valid="filterItems" @reset="filterItems">
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.employee"
              :rules="rules.employee"
              :params="{
                includeEmployees: filters.includeEmployees.param,
              }"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <jobtitle-dropdown v-model="filters.jobTitle" />
          </oxd-grid-item>
          <oxd-grid-item>
            <subunit-dropdown v-model="filters.subunit" />
          </oxd-grid-item>
          <oxd-grid-item>
            <include-employee-dropdown v-model="filters.includeEmployees" />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <review-status-dropdown v-model="filters.status" />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <date-input
              v-model="filters.fromDate"
              :rules="rules.fromDate"
              :label="$t('general.from_date')"
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <date-input
              v-model="filters.toDate"
              :rules="rules.toDate"
              :label="$t('general.to_date')"
            />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            :label="$t('general.reset')"
            type="reset"
          />
          <oxd-button
            class="orangehrm-left-space"
            display-type="secondary"
            :label="$t('general.search')"
            type="submit"
          />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>
    <br />
    <div class="orangehrm-paper-container">
      <table-header
        :selected="0"
        :total="total"
        :loading="isLoading"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:order="sortDefinition"
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
  </div>
</template>

<script>
import {computed, ref, inject} from 'vue';
import {navigate} from '@/core/util/helper/navigation';
import {
  validSelection,
  validDateFormat,
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {
  viewIcon,
  evaluateIcon,
  viewLabel,
  evaluateLabel,
} from '@/orangehrmPerformancePlugin/util/composable/useReviewActions';
import useSort from '@ohrm/core/util/composable/useSort';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@/core/util/services/api.service';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown';
import ReviewStatusDropdown from '@/orangehrmPerformancePlugin/components/ReviewStatusDropdown';
import usei18n from '@/core/util/composable/usei18n';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import IncludeEmployeeDropdown from '@/core/components/dropdown/IncludeEmployeeDropdown';
import ReviewPeriodCell from '@/orangehrmPerformancePlugin/components/ReviewPeriodCell';
import {tableScreenStateKey} from '@ohrm/oxd';

const defaultSortOrder = {
  'employee.lastName': 'DEFAULT',
  'performanceReview.reviewPeriodStart': 'DEFAULT',
  'performanceReview.dueDate': 'DEFAULT',
  'performanceReview.statusId': 'ASC',
};

export default {
  name: 'ReviewList',
  components: {
    'include-employee-dropdown': IncludeEmployeeDropdown,
    'review-status-dropdown': ReviewStatusDropdown,
    'subunit-dropdown': SubunitDropdown,
    'jobtitle-dropdown': JobtitleDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
  },
  props: {
    fromDate: {
      type: String,
      required: false,
      default: null,
    },
    toDate: {
      type: String,
      required: false,
      default: null,
    },
  },
  setup(props) {
    const {$t} = usei18n();
    const {jsDateFormat, userDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const reviewListDateFormat = (date) =>
      formatDate(parseDate(date), jsDateFormat, {locale});

    const reviewListNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          employee: `${item.employee?.firstName} ${item.employee?.lastName} ${
            item.employee?.terminationId
              ? ` ${$t('general.past_employee')}`
              : ''
          }`,
          jobTitle: item.jobTitle?.name,
          subunit: item.subunit?.name,
          reviewPeriod: {
            reviewPeriodStart: reviewListDateFormat(item.reviewPeriodStart),
            reviewPeriodEnd: reviewListDateFormat(item.reviewPeriodEnd),
          },
          dueDate: reviewListDateFormat(item.dueDate),
          status:
            item.overallStatus.statusId === 2
              ? $t('performance.activated')
              : item.overallStatus.statusId === 3
              ? $t('performance.in_progress')
              : $t('performance.completed'),
          statusId: item.overallStatus.statusId,
        };
      });
    };

    const defaultFilters = {
      employee: null,
      jobTitle: null,
      subunit: null,
      status: null,
      fromDate: null,
      toDate: null,
      includeEmployees: {
        id: 1,
        param: 'onlyCurrent',
        label: $t('general.current_employees_only'),
      },
    };

    const filters = ref({
      ...defaultFilters,
      ...(props.fromDate && {fromDate: props.fromDate}),
      ...(props.toDate && {toDate: props.toDate}),
    });
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const serializedFilters = computed(() => {
      return {
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        empNumber: filters.value.employee?.id,
        jobTitleId: filters.value.jobTitle?.id,
        subunitId: filters.value.subunit?.id,
        statusId: filters.value.status?.statusId,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        includeEmployees: filters.value.includeEmployees?.param,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/employees/reviews',
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
      query: serializedFilters,
      normalizer: reviewListNormalizer,
    });

    onSort(execQuery);

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
      filters,
      sortDefinition,
      userDateFormat,
    };
  },
  data() {
    return {
      headers: [
        {
          name: 'employee',
          title: this.$t('general.employee'),
          slot: 'title',
          sortField: 'employee.lastName',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: this.$t('general.job_title'),
          style: {flex: 1},
        },
        {
          name: 'subunit',
          title: this.$t('general.sub_unit'),
          style: {flex: 1},
        },
        {
          name: 'reviewPeriod',
          title: this.$t('performance.review_period'),
          sortField: 'performanceReview.reviewPeriodStart',
          style: {flex: 1},
          cellRenderer: this.reviewPeriodCellRenderer,
        },
        {
          name: 'dueDate',
          title: this.$t('performance.due_date'),
          sortField: 'performanceReview.dueDate',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: this.$t('performance.review_status'),
          sortField: 'performanceReview.statusId',
          style: {flex: 1},
        },
        {
          name: 'action',
          slot: 'footer',
          title: this.$t('general.actions'),
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.actionCellRenderer,
          style: {flex: 1},
        },
      ],
      rules: {
        employee: [shouldNotExceedCharLength(100), validSelection],
        fromDate: [
          validDateFormat(this.userDateFormat),
          startDateShouldBeBeforeEndDate(
            () => this.filters.toDate,
            this.$t('general.from_date_should_be_before_to_date'),
            {allowSameDate: true},
          ),
        ],
        toDate: [
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(
            () => this.filters.fromDate,
            this.$t('general.to_date_should_be_after_from_date'),
            {allowSameDate: true},
          ),
        ],
      },
    };
  },
  methods: {
    actionCellRenderer(...[, , , row]) {
      const cellConfig = {};
      const screenState = inject(tableScreenStateKey);

      if (screenState.screenType === 'lg' || screenState.screenType === 'xl') {
        if (row.statusId === 4) {
          cellConfig.view = viewIcon;
          cellConfig.view.props.title = this.$t('general.view');
          cellConfig.view.onClick = this.onClickEvaluate;
        } else {
          cellConfig.evaluate = evaluateIcon;
          cellConfig.evaluate.props.title = this.$t('performance.evaluate');
          cellConfig.evaluate.onClick = this.onClickEvaluate;
        }
      } else {
        if (row.statusId === 4) {
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
    async filterItems() {
      await this.execQuery();
    },
    onClickEvaluate(item) {
      navigate('/performance/reviewEvaluateByAdmin/{id}', {id: item.id});
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
}
</style>
