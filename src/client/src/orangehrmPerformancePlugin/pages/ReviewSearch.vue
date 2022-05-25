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
    <oxd-table-filter
      :filter-title="$t('performance.manage_performance_reviews')"
    >
      <oxd-form @submitValid="filterItems" @reset="resetDataTable">
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.employee"
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
              :label="$t('general.from_date')"
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <date-input
              v-model="filters.toDate"
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
import {computed, ref} from 'vue';
import useSort from '@ohrm/core/util/composable/useSort';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@/core/util/services/api.service';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import usei18n from '@/core/util/composable/usei18n';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import ReviewStatusDropdown from '@/orangehrmPerformancePlugin/components/ReviewStatusDropdown';
import IncludeEmployeeDropdown from '@/core/components/dropdown/IncludeEmployeeDropdown';
import {navigate} from "@/core/util/helper/navigation";

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
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const reviewListDateFormat = date =>
      formatDate(parseDate(date), jsDateFormat, {locale});

    const statusOpts = [
      {id: 1, label: $t('performance.inactive')},
      {id: 2, label: $t('performance.activated')},
      {id: 3, label: $t('performance.in_progress')},
      {id: 4, label: $t('performance.completed')},
    ];

    const reviewListNormalizer = data => {
      return data.map(item => {
        const employee = item.employee;
        const reviewer = item.reviewer?.employee;
        return {
          id: item.id,
          employee: `${employee?.firstName} ${employee?.lastName} ${
            employee?.terminationId ? ` ${$t('general.past_employee')}` : ''
          }`,
          reviewer: `${reviewer?.firstName} ${reviewer?.lastName} ${
            reviewer?.terminationId ? ` ${$t('general.past_employee')}` : ''
          }`,
          jobTitle: item.jobTitle?.name,
          reviewPeriod: `${reviewListDateFormat(
            item.reviewPeriodStart,
          )} - ${reviewListDateFormat(item.reviewPeriodEnd)}`,
          dueDate: reviewListDateFormat(item.dueDate),
          status: statusOpts.find(el => el.id === item.overallStatus.statusId)
            .label,
          statusName: item.overallStatus.statusName,
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
      'api/v2/performance/manage/reviews',
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
          sortField: 'jobTitle.jobTitleName',
          style: {flex: 1},
        },
        {
          name: 'reviewPeriod',
          title: this.$t('performance.review_period'),
          sortField: 'performanceReview.reviewPeriodStart',
          style: {flex: 1},
        },
        {
          name: 'dueDate',
          title: this.$t('performance.due_date'),
          sortField: 'performanceReview.dueDate',
          style: {flex: 1},
        },
        {
          name: 'reviewer',
          title: this.$t('performance.reviewer'),
          sortField: 'reviewerEmployee.lastName',
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
          cellRenderer: this.cellRenderer,
          style: {flex: 1},
        },
      ],
      checkedItems: [],
    };
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};

      if (row.statusName === 'Completed') {
        cellConfig.view = {
          component: 'oxd-button',
          props: {
            name: 'view',
            label: this.$t('general.view'),
            displayType: 'text',
            size: 'medium',
            style: {
              'min-width': '120px',
            },
          },
        };
      } else if (row.statusName === 'Inactive') {
        cellConfig.edit = {
          onClick: this.onClickEdit,
          component: 'oxd-button',
          props: {
            name: 'edit',
            label: this.$t('general.edit'),
            displayType: 'text',
            size: 'medium',
            style: {
              'min-width': '120px',
            },
          },
        };
      } else {
        cellConfig.evaluate = {
          component: 'oxd-button',
          props: {
            name: 'evaluate',
            label: this.$t('performance.evaluate'),
            displayType: 'text',
            size: 'medium',
            style: {
              'min-width': '120px',
            },
          },
        };
      }

      cellConfig.delete = {
        onClick: this.onClickDelete,
        component: 'oxd-icon-button',
        props: {
          name: 'trash',
        },
      };

      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    onClickEdit(item) {
      navigate('/performance/saveReview/{id}',{id: item.id});
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach(index => {
        ids.push(this.items?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
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
