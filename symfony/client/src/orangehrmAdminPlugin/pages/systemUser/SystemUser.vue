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
    <oxd-table-filter filter-title="System Users">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field label="Username" v-model="filters.username" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="User Role"
                v-model="filters.userRoleId"
                :clear="false"
                :options="userRoles"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-dropdown v-model="filters.empNumber" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Status"
                v-model="filters.status"
                :clear="false"
                :options="userStatuses"
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
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
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
          :order="order"
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
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import EmployeeDropdown from '@/core/components/inputs/EmployeeDropdown';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      userName: item.userName,
      role: item.userRole?.displayName,
      empName: `${item.employee?.firstName} ${item.employee?.lastName}`,
      status: item.status ? 'Enabled' : 'Disabled',
    };
  });
};

const defaultFilters = {
  username: '',
  userRoleId: [{id: 0, label: 'All'}],
  empNumber: [],
  status: [{id: 0, label: 'All'}],
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'employee-dropdown': EmployeeDropdown,
  },

  data() {
    return {
      headers: [
        {name: 'userName', title: 'Username', style: {flex: 1}},
        {name: 'role', title: 'User Role', style: {flex: 1}},
        {
          name: 'empName',
          slot: 'title',
          title: 'Employee Name',
          style: {flex: 1},
        },
        {name: 'status', title: 'Status', style: {flex: 1}},
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
      userRoles: [
        {id: 0, label: 'All'},
        {id: 1, label: 'Admin'},
        {id: 2, label: 'ESS'},
      ],
      userStatuses: [
        {id: 0, label: 'All'},
        {id: 1, label: 'Enabled'},
        {id: 2, label: 'Disabled'},
      ],
      checkedItems: [],
      order: [
        {
          id: 0,
          default: 'desc',
        },
        {
          id: 1,
          default: '',
        },
        {
          id: 2,
          default: '',
        },
        {
          id: 3,
          default: '',
        },
      ],
    };
  },

  setup() {
    const filters = ref({...defaultFilters});
    const serializedFilters = computed(() => {
      return {
        username: '',
        userRoleId: filters.value.userRoleId.map(item => item.id)[0],
        empNumber: filters.value.empNumber.map(item => item.id)[0],
        status: filters.value.status.map(item => item.id)[0],
      };
    });
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/admin/users');
    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, serializedFilters, userdataNormalizer);
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
    };
  },

  methods: {
    onClickAdd() {
      navigate('/admin/saveSystemUser');
    },
    onClickEdit(item) {
      navigate('/admin/saveSystemUser/{id}', {id: item.id});
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
