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
      :filter-title="$t('performance.employee_performance_trackers')"
    >
      <oxd-form @submit-valid="filterItems" @reset="resetDataTable">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.empName"
                :rules="rules.employee"
                api-path="/api/v2/performance/trackers/reviewers"
                :params="{
                  includeEmployees: filters.includeEmployees.param,
                }"
              >
              </employee-autocomplete>
            </oxd-grid-item>
            <oxd-grid-item>
              <include-employee-dropdown
                v-model="filters.includeEmployees"
              ></include-employee-dropdown>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
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
          :clickable="true"
          :loading="isLoading"
          class="orangehrm-employee-list"
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
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useLocale from '@/core/util/composable/useLocale';
import usei18n from '@/core/util/composable/usei18n';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import IncludeEmployeeDropdown from '@/core/components/dropdown/IncludeEmployeeDropdown';
import {
  shouldNotExceedCharLength,
  validSelection,
} from '@/core/util/validation/rules';

const defaultSortOrder = {
  'employee.lastName': 'DEFAULT',
  'tracker.trackerName': 'DEFAULT',
  'tracker.modifiedDate': 'DESC',
  'tracker.addedDate': 'DEFAULT',
};
export default {
  components: {
    'include-employee-dropdown': IncludeEmployeeDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
  },
  setup() {
    const {$t} = usei18n();
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();
    const employeeTrackerNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          title: item.title,
          empName: `${item.employee?.firstName} ${item.employee?.lastName} ${
            item.employee?.terminationId
              ? ` ${$t('general.past_employee')}`
              : ''
          }`,
          modifiedDate: formatDate(parseDate(item.modifiedDate), jsDateFormat, {
            locale,
          }),
          addedDate: formatDate(parseDate(item.addedDate), jsDateFormat, {
            locale,
          }),
        };
      });
    };

    const defaultFilters = {
      empName: null,
      includeEmployees: {
        id: 1,
        param: 'onlyCurrent',
        label: $t('general.current_employees_only'),
      },
    };

    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilter = computed(() => {
      return {
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        empNumber: filters.value.empName?.id,
        includeEmployees: filters.value.includeEmployees?.param,
      };
    });

    const api = '/api/v2/performance/employees/trackers';
    const http = new APIService(window.appGlobal.baseUrl, api);

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      isLoading,
      response,
      execQuery,
    } = usePaginate(http, {
      query: serializedFilter,
      normalizer: employeeTrackerNormalizer,
    });

    onSort(execQuery);

    return {
      total,
      showPaginator,
      currentPage,
      pages,
      pageSize,
      isLoading,
      items: response,
      api,
      http,
      execQuery,
      sortDefinition,
      filters,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'empName',
          slot: 'title',
          title: this.$t('general.employee_name'),
          sortField: 'employee.lastName',
          style: {flex: 2},
        },
        {
          name: 'title',
          title: this.$t('general.trackers'),
          sortField: 'tracker.trackerName',
          style: {flex: 2},
        },
        {
          name: 'addedDate',
          title: this.$t('performance.added_date'),
          sortField: 'tracker.addedDate',
          style: {flex: 1},
        },
        {
          name: 'modifiedDate',
          title: this.$t('performance.modified_date'),
          sortField: 'tracker.modifiedDate',
          style: {flex: 1},
        },
        {
          name: 'actions',
          title: this.$t('general.actions'),
          slot: 'action',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                name: 'view',
                label: this.$t('general.view'),
                class: 'orangehrm-left-space',
                displayType: 'text',
              },
            },
          },
        },
      ],
      rules: {
        employee: [shouldNotExceedCharLength(100), validSelection],
      },
    };
  },

  methods: {
    async resetDataTable() {
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
    onClickView(item) {
      navigate('/performance/addPerformanceTrackerLog/trackId/{id}', {
        id: item.id,
      });
    },
  },
};
</script>
