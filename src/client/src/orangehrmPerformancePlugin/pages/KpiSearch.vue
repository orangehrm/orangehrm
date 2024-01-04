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
      :filter-title="$t('performance.key_performance_indicators_for_job_title')"
    >
      <oxd-form @submit-valid="filterItems" @reset="resetDataTable">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <jobtitle-dropdown v-model="filters.jobTitleId" />
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
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@/core/util/composable/usePaginate';
import useSort from '@/core/util/composable/useSort';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown.vue';
import usei18n from '@/core/util/composable/usei18n';

const defaultFilters = {
  jobTitleId: null,
};

const defaultSortOrder = {
  'kpi.title': 'ASC',
  'jobTitle.jobTitleName': 'DEFAULT',
};

export default {
  name: 'KpiSearch',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'jobtitle-dropdown': JobtitleDropdown,
  },
  setup() {
    const {$t} = usei18n();
    const kpiNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          title: item.title,
          jobTitleName: item.jobTitle.name,
          jobTitleId: item.jobTitle.id,
          minRating: item.minRating,
          maxRating: item.maxRating,
          isDefault: item.isDefault ? $t('general.yes') : '',
          isDeletable: item.deletable,
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
        jobTitleId: filters.value.jobTitleId?.id,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/kpis',
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
      normalizer: kpiNormalizer,
    });

    onSort(execQuery);

    return {
      http,
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      items: response,
      isLoading,
      execQuery,
      filters,
      sortDefinition,
    };
  },
  data() {
    return {
      headers: [
        {
          name: 'title',
          title: this.$t('performance.key_performance_indicator'),
          slot: 'title',
          sortField: 'kpi.title',
          style: {flex: '30%'},
        },
        {
          name: 'jobTitleName',
          title: this.$t('general.job_title'),
          sortField: 'jobTitle.jobTitleName',
          style: {flex: '30%'},
        },
        {
          name: 'minRating',
          title: this.$t('performance.min_rate'),
          style: {flex: '10%'},
        },
        {
          name: 'maxRating',
          title: this.$t('performance.max_rate'),
          style: {flex: '10%'},
        },
        {
          name: 'isDefault',
          title: this.$t('performance.is_default'),
          style: {flex: '10%'},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: '10%'},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
        },
      ],
      checkedItems: [],
    };
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};
      cellConfig.edit = {
        onClick: this.onClickEdit,
        props: {
          name: 'pencil-fill',
        },
      };
      if (row.isDeletable) {
        cellConfig.delete = {
          onClick: this.onClickDelete,
          component: 'oxd-icon-button',
          props: {
            name: 'trash',
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
    onClickAdd() {
      navigate('/performance/saveKpi');
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
    onClickEdit(item) {
      navigate('/performance/saveKpi/{id}', {id: item.id});
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
