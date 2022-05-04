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
    <oxd-table-filter filter-title="Key Performance Indicators for Job Title">
      <oxd-form @submitValid="filterItems" @reset="resetDataTable">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <jobtitle-dropdown v-model="filters.jobTitleId" />
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
      <div class="orangehrm-header-container">
        <oxd-button
          label="Add"
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

const kpiNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      title: item.title,
      jobTitleName: item.jobTitle.name,
      jobTitleId: item.jobTitle.id,
      minRating: item.minRating,
      maxRating: item.maxRating,
      isDefault: item.isDefault ? 'Yes' : '',
    };
  });
};

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
      'api/v2/performance/kpi',
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
          title: 'Key Performance Indicator',
          slot: 'title',
          sortField: 'kpi.title',
          style: {flex: '25%'},
        },
        {
          name: 'jobTitleName',
          title: 'Job Title',
          sortField: 'jobTitle.jobTitleName',
          style: {flex: '25%'},
        },
        {
          name: 'minRating',
          title: 'Min Rate',
          style: {flex: 1},
        },
        {
          name: 'maxRating',
          title: 'Max Rate',
          style: {flex: 1},
        },
        {
          name: 'isDefault',
          title: 'Is Default',
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
    };
  },
  methods: {
    onClickAdd() {
      navigate('/performance/saveKpi');
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
