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
  <oxd-table-filter filter-title="ClaimEvents">
    <oxd-form @submit-valid="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field v-model="filters.name" label="Name" />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="filters.status"
              type="select"
              label="Status"
              :options="ClaimEventStatuses"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions>
        <oxd-button display-type="ghost" label="Reset" @click="onClickReset" />
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
    <div class="orangehrm-header-container">
      <oxd-button
        label="Add"
        icon-name="plus"
        display-type="secondary"
        @click="() => console.log('Hello')"
      />
    </div>
    <table-header
      :total="total"
      :loading="isLoading"
      :selected="checkedItems.length"
    />
    <div class="orangehrm-container">
      <oxd-card-table
        v-model:selected="checkedItems"
        v-model:order="sortDefinition"
        :items="items?.data"
        :headers="headers"
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
</template>

<script>
import {ref, computed, watch} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';

const defaultFilters = {
  name: '',
  status: null,
};

const defaultSortOrder = {
  'claimEvent.name': 'ASC',
  'claimEvent.status': 'DESC',
};

export default {
  setup() {
    const filters = ref({...defaultFilters});
    const filter2 = ref(null);

    watch(filters.value, (newFilter) => {
      console.log(newFilter.status);
      newFilter.status.id == '1'
        ? (filter2.value = true)
        : (filter2.value = false);
    });

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        name: filters.value.name,
        status: filters.value.status ? filters.value.status?.id === 1 : null,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/claim/events',
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
    } = usePaginate(http, {query: serializedFilters});
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
      response,
      filters,
      sortDefinition,
      filter2,
    };
  },

  methods: {
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
    onClickReset() {
      this.filter2 = null;
      this.filters = {...defaultFilters};
      this.filterItems();
    },
  },

  data: () => ({
    headers: [
      {
        name: 'name',
        title: 'Name',
        slot: 'title',
        sortField: 'claimEvent.name',
        style: {'flex-basis': '40%'},
      },
      {
        name: 'status',
        title: 'Status',
        sortField: 'claimEvent.status',
        style: {'flex-basis': '40%'},
      },
      {
        name: 'actions',
        title: 'Actions',
        slot: 'action',
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {
          delete: {
            onClick: () => {
              console.log('delete');
            },
            component: 'oxd-icon-button',
            props: {
              name: 'trash',
            },
          },
          edit: {
            onClick: () => {
              console.log('edit');
            },
            props: {
              name: 'pencil-fill',
            },
          },
        },
      },
    ],
    checkedItems: [],
    ClaimEventStatuses: [
      {id: 0, label: 'Enabled'},
      {id: 1, label: 'Disabled'},
    ],
  }),
};
</script>

<style></style>
