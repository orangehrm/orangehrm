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
    <oxd-table-filter :filter-title="$t('performance.performance_trackers')">
      <oxd-form @submit-valid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.empNumber"
                :rules="rules.employee"
                :params="{
                  includeEmployees: 'currentAndPast',
                }"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            :label="$t('general.reset')"
            @click="onClickReset"
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
          display-type="secondary"
          icon-name="plus"
          :label="$t('general.add')"
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
import useSort from '@/core/util/composable/useSort';
import {navigate} from '@/core/util/helper/navigation';
import useLocale from '@/core/util/composable/useLocale';
import {
  shouldNotExceedCharLength,
  validSelection,
} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@/core/util/composable/usePaginate';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

const defaultFilters = {
  empNumber: null,
};

const defaultSortOrder = {
  'performanceTracker.modifiedDate': 'DESC',
  'employee.lastName': 'ASC',
  'performanceTracker.trackerName': 'ASC',
  'performanceTracker.addedDate': 'DESC',
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
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();

    const trackerNormalizer = (data) => {
      return data.map((row) => {
        return {
          id: row.id,
          tracker: row.trackerName,
          addDate: formatDate(parseDate(row.addedDate), jsDateFormat, {
            locale,
          }),
          modifiedDate: formatDate(parseDate(row.modifiedDate), jsDateFormat, {
            locale,
          }),
          empName: $tEmpName(row.employee, {
            includeMiddle: false,
            excludePastEmpTag: false,
          }),
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/config/trackers',
    );

    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
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
      query: serializedFilters,
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
          title: this.$t('general.employee'),
          slot: 'title',
          sortField: 'employee.lastName',
          style: {flex: 1},
        },
        {
          name: 'tracker',
          title: this.$t('performance.tracker'),
          style: {flex: 1},
          sortField: 'performanceTracker.trackerName',
        },
        {
          name: 'addDate',
          title: this.$t('performance.added_date'),
          sortField: 'performanceTracker.addedDate',
          style: {flex: 1},
        },
        {
          name: 'modifiedDate',
          title: this.$t('performance.modified_date'),
          sortField: 'performanceTracker.modifiedDate',
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
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
      rules: {
        employee: [shouldNotExceedCharLength(100), validSelection],
      },
    };
  },

  methods: {
    onClickAdd() {
      navigate('/performance/addPerformanceTracker');
    },
    onClickEdit(item) {
      navigate('/performance/addPerformanceTracker/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map((index) => {
        return this.response?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      const isSelectable = this.unselectableIds.findIndex(
        (id) => id == item.id,
      );
      if (isSelectable > -1) {
        return this.$toast.cannotDelete();
      }
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
