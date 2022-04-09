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
    <oxd-table-filter :filter-title="$t('admin.system_users')">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.username"
                :label="$t('general.username')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.userRoleId"
                type="select"
                :label="$t('general.user_role')"
                :options="userRoles"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-autocomplete v-model="filters.empNumber" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.status"
                type="select"
                :label="$t('general.status')"
                :options="userStatuses"
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
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import useSort from '@ohrm/core/util/composable/useSort';

const defaultFilters = {
  username: '',
  userRoleId: null,
  empNumber: null,
  status: null,
};

const defaultSortOrder = {
  'u.userName': 'ASC',
  'r.displayName': 'ASC',
  'e.firstName': 'ASC',
  'u.status': 'DEFAULT',
};

export default {
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

  setup(props) {
    const userdataNormalizer = data => {
      return data.map(item => {
        const selectable = props.unselectableIds.findIndex(id => id == item.id);
        return {
          id: item.id,
          userName: item.userName,
          role: item.userRole?.displayName,
          empName: `${item.employee?.firstName} ${item.employee?.lastName}
          ${item.employee?.terminationId ? ' (Past Employee)' : ''}`,
          status: item.status ? 'Enabled' : 'Disabled',
          isSelectable: selectable === -1,
        };
      });
    };

    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const serializedFilters = computed(() => {
      return {
        username: filters.value.username,
        userRoleId: filters.value.userRoleId?.id,
        empNumber: filters.value.empNumber?.id,
        status: filters.value.status?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
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

  data() {
    return {
      headers: [
        {
          name: 'userName',
          title: this.$t('general.username'),
          sortField: 'u.userName',
          style: {flex: 1},
        },
        {
          name: 'role',
          title: this.$t('general.user_role'),
          style: {flex: 1},
          sortField: 'r.displayName',
        },
        {
          name: 'empName',
          slot: 'title',
          title: this.$t('general.employee_name'),
          sortField: 'e.firstName',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          sortField: 'u.status',
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
      userRoles: [
        {id: 1, label: this.$t('general.admin')},
        {id: 2, label: this.$t('general.ess')},
      ],
      userStatuses: [
        {id: 1, label: this.$t('general.enabled')},
        {id: 2, label: this.$t('general.disabled')},
      ],
      checkedItems: [],
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
