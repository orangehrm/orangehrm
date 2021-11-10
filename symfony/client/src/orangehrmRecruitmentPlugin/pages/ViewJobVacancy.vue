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
    <oxd-table-filter filter-title="Vacancies">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <jobtitle-dropdown v-model="filters.jobTitleId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <vacancy-dropdown v-model="filters.vacancyId" label="Vacancy" />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.hiringManagerId"
                label="Hiring Manager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Status"
                v-model="filters.status"
                :clear="false"
                :options="statusOptions"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button displayType="ghost" label="Reset" @click="onClickReset" />
          <oxd-button
            class="orangehrm-left-space"
            displayType="secondary"
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
          iconName="plus"
          displayType="secondary"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
        :total="total"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items?.data"
          :loading="isLoading"
          :selectable="true"
          :clickable="false"
          rowDecorator="oxd-table-decorator-card"
          v-model:order="sortDefinition"
          v-model:selected="checkedItems"
          class="orangehrm-vacancy-list"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          :length="pages"
          v-model:current="currentPage"
        />
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@orangehrm/core/util/composable/useSort';

import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown.vue';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      vacancy: item.name,
      jobTitle: item.jobTitle?.isDeleted
        ? item.jobTitle.title + ' (Deleted)'
        : item.jobTitle?.title,
      hiringManger: `${item.hiringManager.firstName} ${item.hiringManager.lastName}`,
      status: item.status == 1 ? 'Active' : 'Closed',
    };
  });
};

const defaultFilters = {
  jobTitleId: null,
  hiringManagerId: null,
  vacancyId: null,
  status: null,
};
const defaultSortOrder = {
  'vacancy.name': 'ASC',
  'vacancy.jobTitle': 'DEFAULT',
  'vacancy.employee': 'DEFAULT',
  'vacancy.status': 'DEFAULT',
};
export default {
  name: 'view-job-vacancy',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'jobtitle-dropdown': JobtitleDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
    'vacancy-dropdown': VacancyDropdown,
  },

  data() {
    return {
      headers: [
        {
          name: 'vacancy',
          slot: 'Vacancy',
          title: 'Vacancy',
          sortField: 'vacancy.name',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: 'Job Title',
          sortField: 'vacancy.jobTitle',
          style: {flex: 1},
        },
        {
          name: 'hiringManger',
          title: 'Hiring Manager',
          sortField: 'vacancy.employee',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: 'Status',
          sortField: 'vacancy.status',
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
      statusOptions: [
        {id: 1, param: 'active', label: 'Active'},
        {id: 0, param: 'closed', label: 'Closed'},
      ],
      vacancies: [],
      checkedItems: [],
    };
  },
  setup() {
    const filters = ref({...defaultFilters});
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        vacancyId: filters.value.vacancyId?.id,
        jobTitleId: filters.value.jobTitleId?.id,
        hiringManagerId: filters.value.hiringManagerId?.id,
        status: filters.value.status?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/recruitment/vacancies',
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
      normalizer: userdataNormalizer,
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

  methods: {
    onClickAdd() {
      navigate('/recruitment/addJobVacancy');
    },
    onClickEdit(item) {
      navigate('/recruitment/addJobVacancy/{id}', {id: item.id});
    },

    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      console.log(this.checkedItems);
      const ids = this.checkedItems.map(index => {
        console.log(this.items?.data[index].id);
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData(ids);
        }
      });
    },

    async deleteData(items) {
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

<style src="./vacancy.scss" lang="scss" scoped></style>
