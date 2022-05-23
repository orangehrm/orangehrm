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
    <oxd-table-filter :filter-title="$t('performance.review_list')">
      <oxd-form @submitValid="filterItems" @reset="filterItems">
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
import {computed, ref} from 'vue';
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

const defaultSortOrder = {
  'employee.lastName': 'DEFAULT',
  'performanceReview.workPeriodStart': 'DEFAULT',
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
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const reviewListDateFormat = date =>
      formatDate(parseDate(date), jsDateFormat, {locale});

    const reviewListNormalizer = data => {
      return data.map(item => {
        return {
          id: item.id,
          employee: `${item.employee?.firstName} ${item.employee?.lastName} ${
            item.employee?.terminationId
              ? ` ${$t('general.past_employee')}`
              : ''
          }`,
          jobTitle: item.jobTitle?.name,
          department: item.department?.name,
          reviewPeriod: `${reviewListDateFormat(
            item.reviewPeriodStart,
          )} - ${reviewListDateFormat(item.reviewPeriodEnd)}`,
          dueDate: reviewListDateFormat(item.dueDate),
          status:
            item.status === 2
              ? $t('performance.activated')
              : item.status === 3
              ? $t('performance.in_progress')
              : $t('performance.completed'),
          statusId: item.status,
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
      'api/v2/performance/reviews/list',
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
          name: 'department',
          title: this.$t('performance.department'),
          style: {flex: 1},
        },
        {
          name: 'reviewPeriod',
          title: this.$t('performance.review_period'),
          sortField: 'performanceReview.workPeriodStart',
          style: {flex: 1},
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
          cellRenderer: this.cellRenderer,
          style: {flex: 1},
        },
      ],
    };
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};

      if (row.statusId === 3) {
        cellConfig.view = {
          component: 'oxd-button',
          props: {
            name: 'view',
            label: this.$t('general.view'),
            displayType: 'text',
            size: 'medium',
            style: {
              'min-width': '140px',
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
              'min-width': '140px',
            },
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
    async filterItems() {
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
