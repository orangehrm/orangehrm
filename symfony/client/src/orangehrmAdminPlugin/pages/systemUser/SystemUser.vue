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
                v-model="filters.role"
                :clear="false"
                :options="userRoles"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Employee Name"
                v-model="filters.empName"
                :create-options="loadEmployees"
                :lazyLoad="true"
              />
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
          <oxd-button displayType="secondary" label="Search" type="submit" />
          <oxd-button
            class="orangehrm-left-space"
            displayType="ghost"
            label="Reset"
          />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>
    <br />
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-button label="Add" displayType="secondary" @click="onClickAdd" />
      </div>
      <oxd-divider class="orangehrm-horizontal-margin" />
      <div>
        <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
          <div v-if="checkedItems.length > 0">
            <oxd-text tag="span">
              {{ checkedItems.length }} System Users Selected
            </oxd-text>
            <oxd-button
              label="Delete Selected"
              displayType="label-danger"
              @click="onClickDeleteSelected"
              class="orangehrm-horizontal-margin"
            />
          </div>
          <oxd-text tag="span" v-else>{{ itemsCountText }}</oxd-text>
        </div>
      </div>
      <div class="orangehrm-container">
        <oxd-card-table
          ref="dTable"
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          v-model:selected="checkedItems"
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
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import {navigate} from '@orangehrm/core/util/helper/navigation';

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

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },

  data() {
    return {
      headers: [
        {name: 'userName', title: 'Username', style: {flex: 1}},
        {name: 'role', title: 'User Role', style: {flex: 1}},
        {name: 'empName', title: 'Employee Name', style: {flex: 1}},
        {name: 'status', title: 'Status', style: {flex: 1}},
        {
          name: 'actions',
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
      filters: {
        username: '',
        role: [{id: 0, label: 'All'}],
        empName: [],
        status: [{id: 0, label: 'All'}],
      },
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
      editItem: null,
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
    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate('api/v1/admin/users', userdataNormalizer);
    return {
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
    };
  },

  computed: {
    itemsCountText() {
      return this.total === 0
        ? 'No Records Found'
        : `${this.total} System User Found`;
    },
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
      // TODO: Loading
      if (items instanceof Array) {
        this.$http
          .delete('api/v1/admin/users', {
            data: {ids: items},
          })
          .then(() => {
            this.resetDataTable();
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    async resetDataTable() {
      this.$refs.dTable.checkedItems = [];
      await this.execQuery();
    },
    filterItems() {
      console.log(this.filters);
    },
    async loadEmployees() {
      return new Promise(resolve => {
        setTimeout(() => {
          resolve([
            {
              id: 1,
              label: 'James Fox',
            },
            {
              id: 2,
              label: 'Darth Vader',
            },
            {
              id: 3,
              label: 'J Jhona Jamerson Jr.',
            },
          ]);
        }, 5000);
      });
    },
  },
};
</script>
