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
    <oxd-table-filter filter-title="Performance Trackers">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete v-model="filters.empNumber" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            label="Reset"
            @click="onClickReset"
          />
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
          display-type="secondary"
          icon-name="plus"
          label="Add"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :loading="isLoading"
        :selected="checkedItems.length"
        :total="total"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:order="sortDefinition"
          v-model:selected="checkedItems"
          :clickable="false"
          :headers="headers"
          :items="response?.data"
          :loading="isLoading"
          :selectable="true"
          row-decorator="oxd-table-decorator-card"
        />
      </div>
      <!-- Pagination comes here -->
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          v-model:current="currentPage"
          :length="pages"
        ></oxd-pagination>
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@/core/util/composable/useSort';
import usePaginate from '@/core/util/composable/usePaginate';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';

const trackerNormalizer = data => {
  return data.map(row => {
    return {
      id: row.id,
      tracker: row.trackerName,
      addDate: row.addedDate,
      matureDate: row.modifiedDate,
      empName: `${row.employee?.firstName} ${row.employee?.lastName}
          ${row.employee?.terminationId ? ' (Past Employee)' : ''}`,
    };
  });
};

const defaultFilters = {
  empNumber: null,
  includeEmployees: {
    param: 'currentAndPast',
  },
};

const defaultSortOrder = {
  'e.name': 'ASC',
  't.trackerName': 'ASC',
  't.addDate': 'ASC',
  't.matDate': 'ASC',
};

export default {
  name: 'TrackerList',

  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'employee-autocomplete': EmployeeAutocomplete,
  },
  props: {
    unselectableIds: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const http = new APIService(
      'https://796aa478-538c-47e3-8133-bc2f05a479b1.mock.pstmn.io',
      '/api/v2/performance/addPerformanceTracker',
    );

    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFIlters = computed(() => {
      return {
        empNumber: filters.value.empNumber?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        includeEmployees: filters.value.includeEmployees?.param,
      };
    });
    const {
      showPaginator,
      currentPage,
      total,
      response,
      pages,
      isLoading,
      execQuery,
    } = usePaginate(http, {
      query: serializedFIlters,
      normalizer: trackerNormalizer,
      prefetch: true,
      toastNoRecords: true,
    });

    onSort(execQuery);

    return {
      http,
      total,
      isLoading,
      showPaginator,
      currentPage,
      pages,
      response,
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
          title: 'Employee',
          slot: 'title',
          sortField: 'e.name',
          style: {flex: 1},
        },
        {
          name: 'tracker',
          title: 'Tracker',
          style: {flex: 1},
          sortField: 't.trackerName',
        },
        {
          name: 'addDate',
          title: 'Added Date',
          sortField: 't.addDate',
          style: {flex: 1},
        },
        {
          name: 'matureDate',
          title: 'Matured Date',
          sortField: 't.matDate',
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: 'Actions',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ],
      checkedItems: [],
      /*items: [
        {
          "id":"45",
          "employee":"John",
          "tracker":"testingtitle",
        },
        {
          "id":"56",
          "employee":"jacob",
          "tracker":"testingtitle2",

        }
      ],*/
    };
  },

  methods: {
    onClickAdd() {
      navigate('/performance/savePerformanceTracker');
    },
    onClickEdit(item) {
      navigate('/performance/savePerformanceTracker/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      const isSelectable = this.unselectableIds.findIndex(id => id == item.id);
      if (isSelectable > -1) {
        return this.$toast.cannotDelete();
      }
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
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
    onClickReset() {
      this.filters = {...defaultFilters};
      this.filterItems();
    },
  },
};
</script>

<style></style>
