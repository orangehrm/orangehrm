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
    <oxd-table-filter filter-title="Projects">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <customer-autocomplete
                v-model="filters.customerId"
                label="Customer"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <project-autocomplete
                v-model="filters.projectId"
                label="Project"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.projectAdminEmpNumber"
                label="Project Admin"
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
          :selectable="true"
          :clickable="false"
          v-model:selected="checkedItems"
          :loading="isLoading"
          rowDecorator="oxd-table-decorator-card"
          v-model:order="sortDefinition"
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
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@ohrm/core/util/composable/useSort';

import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';
import CustomerAutocomplete from '@/orangehrmTimePlugin/components/CustomerAutocomplete.vue';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      project: item.name,
      customer: item.customer?.deleted
        ? item.customer?.name + ' (Deleted)'
        : item.customer?.name,
      projectAdmins: item.projectAdmins
        ?.map(projectAdmin => {
          return projectAdmin.terminationId
            ? `${projectAdmin.firstName} ${projectAdmin.lastName} (Past Employee)`
            : `${projectAdmin.firstName} ${projectAdmin.lastName}`;
        })
        .join(),
    };
  });
};

const defaultFilters = {
  customerId: null,
  projectId: null,
  projectAdminEmpNumber: null,
};

const defaultSortOrder = {
  'project.name': 'ASC',
  'customer.name': 'DEFAULT',
  'employee.lastName': 'DEFAULT',
};

export default {
  name: 'project',
  components: {
    'project-autocomplete': ProjectAutocomplete,
    'customer-autocomplete': CustomerAutocomplete,
    'employee-autocomplete': EmployeeAutocomplete,
    'delete-confirmation': DeleteConfirmationDialog,
  },
  data() {
    return {
      headers: [
        {
          name: 'customer',
          title: 'Customer',
          sortField: 'customer.name',
          style: {flex: 2},
        },
        {
          name: 'project',
          slot: 'title',
          title: 'Project',
          sortField: 'project.name',
          style: {flex: 3},
        },
        {
          name: 'projectAdmins',
          title: 'Project Admins',
          style: {flex: 3},
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
      projects: [],
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
        customerId: filters.value.customerId?.id,
        projectId: filters.value.projectId?.id,
        empNumber: filters.value.projectAdminEmpNumber?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        model: 'detailed',
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/time/projects',
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
      navigate('/time/saveProject');
    },
    onClickEdit(item) {
      navigate('/time/saveProject/{id}', {id: item.id});
    },

    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteData([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
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

<style lang="scss" scoped></style>
