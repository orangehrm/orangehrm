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
    <oxd-table-filter filter-title="Employee Information">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-dropdown v-model="filters.employee" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Employee Id"
                v-model="filters.employeeId"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employment-status-dropdown v-model="filters.empStatusId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Include"
                v-model="filters.includeEmployees"
                :clear="false"
                :options="includeOpts"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <supervisor-dropdown v-model="filters.supervisor" />
            </oxd-grid-item>
            <oxd-grid-item>
              <jobtitle-dropdown v-model="filters.jobTitleId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <subunit-dropdown v-model="filters.subunitId" />
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
          class="orangehrm-employee-list"
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
import SupervisorDropdown from '@/core/components/inputs/SupervisorDropdown';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown';
import EmploymentStatusDropdown from '@/orangehrmPimPlugin/components/EmploymentStatusDropdown';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.empNumber,
      employeeId: item.employeeId,
      firstAndMiddleName: `${item.firstName} ${item.middleName}`,
      lastName: item.lastName,
      jobTitle: item.jobTitle?.isDeleted
        ? item.jobTitle.title + ' (Deleted)'
        : item.jobTitle?.title,
      empStatus: item.empStatus?.name,
      subunit: item.subunit?.name,
      supervisor: item.supervisors
        ? item.supervisors
            .map(supervisor => `${supervisor.firstName} ${supervisor.lastName}`)
            .join(',')
        : '',
    };
  });
};

const defaultFilters = {
  employee: [],
  employeeId: '',
  empStatusId: [{id: 0, label: 'All'}],
  includeEmployees: [{id: 1, label: 'Current Employees Only'}],
  supervisor: [],
  jobTitleId: [{id: 0, label: 'All'}],
  subunitId: [{id: 0, label: 'All'}],
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'employee-dropdown': EmployeeDropdown,
    'supervisor-dropdown': SupervisorDropdown,
    'jobtitle-dropdown': JobtitleDropdown,
    'subunit-dropdown': SubunitDropdown,
    'employment-status-dropdown': EmploymentStatusDropdown,
  },

  data() {
    return {
      headers: [
        {name: 'employeeId', slot: 'title', title: 'Id', style: {flex: 1}},
        {
          name: 'firstAndMiddleName',
          title: 'First (& Middle) Name',
          style: {flex: 1},
        },
        {name: 'lastName', title: 'Last Name', style: {flex: 1}},
        {name: 'jobTitle', title: 'Job Title', style: {flex: 1}},
        {name: 'empStatus', title: 'Employment Status', style: {flex: 1}},
        {name: 'subunit', title: 'Sub Unit', style: {flex: 1}},
        {name: 'supervisor', title: 'Supervisor', style: {flex: 1}},
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
      includeOpts: [
        {id: 1, label: 'Current Employees Only'},
        {id: 2, label: 'Current and Past Employees'},
        {id: 3, label: 'Past Employees Only'},
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
        {
          id: 4,
          default: '',
        },
        {
          id: 5,
          default: '',
        },
        {
          id: 6,
          default: '',
        },
      ],
    };
  },

  setup() {
    const filters = ref({...defaultFilters});
    const serializedFilters = computed(() => {
      return {
        model: 'detailed',
        empNumber: filters.value.employee.map(item => item.id)[0],
        employeeId: filters.value.employeeId,
        empStatusId: filters.value.empStatusId.map(item => item.id)[0],
        includeEmployees: filters.value.includeEmployees.map(item => {
          return item.id === 1
            ? 'onlyCurrent'
            : item.id === 2
            ? 'currentAndPast'
            : 'onlyPast';
        })[0],
        supervisorEmpNumbers: filters.value.supervisor.map(item => item.id),
        jobTitleId: filters.value.jobTitleId.map(item => item.id)[0],
        subunitId: filters.value.subunitId.map(item => item.id)[0],
      };
    });
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
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
      navigate('/pim/addEmployee');
    },
    onClickEdit(item) {
      navigate('/pim/viewPersonalDetails/empNumber/{id}', {id: item.id});
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

<style src="./employee.scss" lang="scss" scoped></style>
