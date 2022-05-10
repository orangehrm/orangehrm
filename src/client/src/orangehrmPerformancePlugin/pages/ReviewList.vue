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
    <oxd-table-filter filter-title="Review List">
      <oxd-form @submitValid="filterItems" @reset="filterItems">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.employee"
              :params="{
                includeEmployees: 'onlyCurrent',
              }"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <jobtitle-dropdown v-model="filters.jobTitle" />
          </oxd-grid-item>
          <oxd-grid-item>
            <subunit-dropdown v-model="filters.subUnit" />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <oxd-input-field
              v-model="filters.status"
              type="select"
              label="Status"
              :clear="false"
              :options="statusOpts"
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
import usei18n from '@/core/util/composable/usei18n';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';

const defaultFilters = {
  employee: null,
  jobTitle: null,
  subUnit: null,
  status: null,
  fromDate: null,
  toDate: null,
};

const defaultSortOrder = {
  'employee.firstName': 'DEFAULT',
  'performanceReview.workPeriodStart': 'DEFAULT',
  'performanceReview.dueDate': 'DEFAULT',
  'performanceReview.statusId': 'ASC',
};

export default {
  name: 'ReviewList',
  components: {
    'subunit-dropdown': SubunitDropdown,
    'jobtitle-dropdown': JobtitleDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
  },
  setup() {
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
            item.workPeriodStart,
          )} - ${reviewListDateFormat(item.workPeriodEnd)}`,
          dueDate: reviewListDateFormat(item.dueDate),
          status:
            item.status === 2
              ? 'Activated'
              : item.status === 3
              ? 'In Progress'
              : 'Completed',
        };
      });
    };

    const filters = ref({...defaultFilters});
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const serializedFilters = computed(() => {
      return {
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        empNumber: filters.value.employee?.id,
        jobTitleId: filters.value.jobTitle?.id,
        subUnitId: filters.value.subUnit?.id,
        statusId: filters.value.status?.statusId,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/performance/review-lists',
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
          sortField: 'employee.firstName',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: this.$t('general.job_title'),
          style: {flex: 1},
        },
        {
          name: 'department',
          title: 'Department',
          style: {flex: 1},
        },
        {
          name: 'reviewPeriod',
          title: 'Review Period',
          sortField: 'performanceReview.workPeriodStart',
          style: {flex: 1},
        },
        {
          name: 'dueDate',
          title: 'Due Date',
          sortField: 'performanceReview.dueDate',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: 'Status',
          sortField: 'performanceReview.statusId',
          style: {flex: 1},
        },
        {
          name: 'action',
          slot: 'action',
          title: this.$t('general.actions'),
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
          style: {flex: 1},
        },
      ],
      statusOpts: [
        {id: 1, statusId: 2, label: 'Activated'},
        {id: 2, statusId: 3, label: 'In Progress'},
        {id: 3, statusId: 4, label: 'Completed'},
      ],
    };
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};

      if (row.status === 'Completed') {
        cellConfig.view = {
          component: 'oxd-button',
          props: {
            name: 'view',
            label: this.$t('general.view'),
            class: 'orangehrm-left-space',
            displayType: 'text',
            size: 'medium',
          },
        };
      } else {
        cellConfig.evaluate = {
          component: 'oxd-button',
          props: {
            name: 'evaluate',
            label: 'Evaluate',
            class: 'orangehrm-left-space',
            displayType: 'text',
            size: 'medium',
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
