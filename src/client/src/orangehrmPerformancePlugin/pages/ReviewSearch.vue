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
    <oxd-table-filter
      :filter-title="$t('performance.manage_performance_reviews')"
    >
      <oxd-form @submit-valid="filterItems" @reset="resetDataTable">
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
            <review-status-dropdown
              v-model="filters.status"
              :options="statusOpts"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <include-employee-dropdown v-model="filters.includeEmployees" />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <!-- All reviewers are searchable regardless of include employees param -->
            <employee-autocomplete
              v-model="filters.reviewer"
              :label="$t('performance.reviewer')"
              :params="{
                includeEmployees: 'currentAndPast',
              }"
            />
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
      <div class="orangehrm-header-container">
        <oxd-button
          :label="$t('general.add')"
          icon-name="plus"
          display-type="secondary"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
          :selectable="true"
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
    <delete-confirmation-dialog ref="deleteDialog"></delete-confirmation-dialog>
  </div>
</template>

<script>
import {computed, ref, inject} from 'vue';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  validSelection,
  validDateFormat,
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {
  viewIcon,
  editIcon,
  evaluateIcon,
  viewLabel,
  editLabel,
  evaluateLabel,
} from '@/orangehrmPerformancePlugin/util/composable/useReviewActions';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useSort from '@ohrm/core/util/composable/useSort';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import usei18n from '@/core/util/composable/usei18n';
import useLocale from '@/core/util/composable/useLocale';
import useDateFormat from '@/core/util/composable/useDateFormat';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import ReviewStatusDropdown from '@/orangehrmPerformancePlugin/components/ReviewStatusDropdown';
import IncludeEmployeeDropdown from '@/core/components/dropdown/IncludeEmployeeDropdown';
import ReviewPeriodCell from '@/orangehrmPerformancePlugin/components/ReviewPeriodCell';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {tableScreenStateKey} from '@ohrm/oxd';

const defaultSortOrder = {
  'employee.lastName': 'DEFAULT',
  'performanceReview.reviewPeriodStart': 'DEFAULT',
  'performanceReview.dueDate': 'DEFAULT',
  'performanceReview.statusId': 'ASC',
  'jobTitle.jobTitleName': 'DEFAULT',
  'reviewerEmployee.lastName': 'DEFAULT',
};

export default {
  name: 'ReviewSearch',
  components: {
    'include-employee-dropdown': IncludeEmployeeDropdown,
    'review-status-dropdown': ReviewStatusDropdown,
    'jobtitle-dropdown': JobtitleDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
    'delete-confirmation-dialog': DeleteConfirmationDialog,
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
    const {$tEmpName} = useEmployeeNameTranslate();
    const reviewListDateFormat = (date) =>
      formatDate(parseDate(date), jsDateFormat, {locale});

    const statusOpts = [
      {id: 1, label: $t('performance.inactive')},
      {id: 2, label: $t('performance.activated')},
      {id: 3, label: $t('performance.in_progress')},
      {id: 4, label: $t('performance.completed')},
    ];

    const reviewListNormalizer = (data) => {
      return data.map((item) => {
        const employee = item.employee;
        const reviewer = item.reviewer?.employee;
        return {
          id: item.id,
          employee: $tEmpName(employee),
          reviewer: $tEmpName(reviewer),
          jobTitle: item.jobTitle?.name,
          reviewPeriod: {
            reviewPeriodStart: reviewListDateFormat(item.reviewPeriodStart),
            reviewPeriodEnd: reviewListDateFormat(item.reviewPeriodEnd),
          },
          dueDate: reviewListDateFormat(item.dueDate),
          status: statusOpts.find((el) => el.id === item.overallStatus.statusId)
            .label,
          statusId: item.overallStatus.statusId,
        };
      });
    };

    const defaultFilters = {
      employee: null,
      jobTitle: null,
      status: null,
      reviewer: null,
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
        statusId: filters.value.status?.id,
        reviewerEmpNumber: filters.value.reviewer?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        includeEmployees: filters.value.includeEmployees?.param,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/manage/reviews',
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
      statusOpts,
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
          style: {flex: '14%'},
        },
        {
          name: 'jobTitle',
          title: this.$t('general.job_title'),
          sortField: 'jobTitle.jobTitleName',
          style: {flex: '14%'},
        },
        {
          name: 'reviewPeriod',
          title: this.$t('performance.review_period'),
          sortField: 'performanceReview.reviewPeriodStart',
          style: {flex: '14%'},
          cellRenderer: this.reviewPeriodCellRenderer,
        },
        {
          name: 'dueDate',
          title: this.$t('performance.due_date'),
          sortField: 'performanceReview.dueDate',
          style: {flex: '14%'},
        },
        {
          name: 'reviewer',
          title: this.$t('performance.reviewer'),
          sortField: 'reviewerEmployee.lastName',
          style: {flex: '14%'},
        },
        {
          name: 'status',
          title: this.$t('performance.review_status'),
          sortField: 'performanceReview.statusId',
          style: {flex: '14%'},
        },
        {
          name: 'action',
          slot: 'footer',
          title: this.$t('general.actions'),
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.actionButtonCellRenderer,
          style: {flex: '16%'},
        },
      ],
      checkedItems: [],
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
    actionButtonCellRenderer(...[, , , row]) {
      const cellConfig = {};
      const screenState = inject(tableScreenStateKey);

      cellConfig.delete = {
        onClick: this.onClickDelete,
        component: 'oxd-icon-button',
        props: {
          name: 'trash',
        },
      };

      if (screenState.screenType === 'lg' || screenState.screenType === 'xl') {
        if (row.statusId === 4) {
          cellConfig.view = viewIcon;
          cellConfig.view.props.title = this.$t('general.view');
          cellConfig.view.onClick = this.onClickEvaluate;
        } else if (row.statusId === 1) {
          cellConfig.edit = editIcon;
          cellConfig.edit.props.title = this.$t('general.edit');
          cellConfig.edit.onClick = this.onClickEdit;
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
        } else if (row.statusId === 1) {
          cellConfig.edit = editLabel;
          cellConfig.edit.props.label = this.$t('general.edit');
          cellConfig.edit.onClick = this.onClickEdit;
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
    onClickEdit(item) {
      navigate('/performance/saveReview/{id}', {id: item.id});
    },
    onClickAdd() {
      navigate('/performance/saveReview');
    },
    onClickEvaluate(item) {
      navigate('/performance/reviewEvaluateByAdmin/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach((index) => {
        ids.push(this.items?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    deleteItems(items) {
      if (items instanceof Array) {
        this.isLoading = true;
        this.http
          .deleteAll({
            ids: items,
          })
          .then(() => {
            return this.$toast.deleteSuccess();
          })
          .then(() => {
            this.isLoading = false;
            this.resetDataTable();
          });
      }
    },
    async filterItems() {
      await this.execQuery();
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
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
