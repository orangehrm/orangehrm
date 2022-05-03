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
    <oxd-table-filter filter-title="Employee Performance Trackers">
      <oxd-form @submitValid="filterItems" @reset="resetDataTable">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.empName"
                :params="{
                  includeEmployees: filters.includeEmployees.param,
                }"
              >
              </employee-autocomplete>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.includeEmployees"
                type="select"
                label="Include"
                :clear="false"
                :options="includeOpts"
                :show-empty-selector="false"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button display-type="ghost" label="Reset" type="reset" />
          <oxd-button
            class="orangehrm-left-space"
            display-type="secondary"
            label="Search"
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
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';

const defaultFilters = {
  empName: null,
  includeEmployees: {
    id: 1,
    param: 'onlyCurrent',
    label: 'Current Employees Only',
  },
};

const defaultSortOrder = {
  empName: 'DEFAULT',
  trackers: 'DEFAULT',
  modifiedDate: 'DESC',
  addedDate: 'DEFAULT',
};
export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },
  setup() {
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

    const http = new APIService(
      'https://5875ebe4-9692-48a9-90dc-b79dac993a70.mock.pstmn.io',
      '/api/v2/Performance/EmployeeTrackers',
    );
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
          title: 'Employee Name',
          sortField: 'empName',
          style: {flex: 2},
        },
        {
          name: 'trackers',
          title: 'Trackers',
          sortField: 'trackers',
          style: {flex: 2},
        },
        {
          name: 'modifiedDate',
          title: 'Modified Date',
          sortField: 'modifiedDate',
          style: {flex: 1},
        },
        {
          name: 'addedDate',
          title: 'Added Date',
          sortField: 'addedDate',
          style: {flex: 1},
        },
        {
          name: 'actions',
          title: 'Actions',
          slot: 'action ',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                name: 'view',
                label: 'View',
                class: 'orangehrm-left-space',
                displayType: 'ghost',
              },
            },
          },
        },
      ],
      includeOpts: [
        {id: 1, param: 'onlyCurrent', label: 'Current Employees Only'},
        {id: 2, param: 'currentAndPast', label: 'Current and Past Employees'},
        {id: 3, param: 'onlyPast', label: 'Past Employees Only'},
      ],
    };
  },

  methods: {
    async resetDataTable() {
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
  },
};
</script>
