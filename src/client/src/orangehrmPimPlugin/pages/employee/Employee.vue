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
    <oxd-table-filter :filter-title="$t('pim.employee_information')">
      <oxd-form @submitValid="filterItems" @reset="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.employee"
                :params="{
                  includeEmployees: filters.includeEmployees?.param,
                }"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.employeeId"
                :label="$t('general.employee_id')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employment-status-dropdown v-model="filters.empStatusId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.includeEmployees"
                type="select"
                :label="$t('pim.include')"
                :clear="false"
                :options="includeOpts"
                :show-empty-selector="false"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.supervisor"
                :label="$t('pim.supervisor_name')"
              />
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
      <div
        v-if="$can.create('employee_list')"
        class="orangehrm-header-container"
      >
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
          ref="cardTable"
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
          :selectable="$can.delete('employee_list')"
          :clickable="true"
          :loading="isLoading"
          class="orangehrm-employee-list"
          row-decorator="oxd-table-decorator-card"
          @click="onClickEdit"
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
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown';
import EmploymentStatusDropdown from '@/orangehrmPimPlugin/components/EmploymentStatusDropdown';
import useSort from '@ohrm/core/util/composable/useSort';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.empNumber,
      employeeId: item.employeeId,
      firstAndMiddleName: `${item.firstName} ${item.middleName}`,
      lastName: item.lastName + (item.terminationId ? ' (Past Employee)' : ''),
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
  employee: null,
  employeeId: '',
  empStatusId: null,
  includeEmployees: {
    id: 1,
    param: 'onlyCurrent',
    label: 'Current Employees Only',
  },
  supervisor: null,
  jobTitleId: null,
  subunitId: null,
};

const defaultSortOrder = {
  'employee.employeeId': 'DEFAULT',
  'employee.firstName': 'ASC',
  'employee.lastName': 'DEFAULT',
  'jobTitle.jobTitleName': 'DEFAULT',
  'empStatus.name': 'DEFAULT',
  'subunit.name': 'DEFAULT',
  'supervisor.firstName': 'DEFAULT',
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
    'subunit-dropdown': SubunitDropdown,
    'employment-status-dropdown': EmploymentStatusDropdown,
  },

  setup() {
    const filters = ref({...defaultFilters});
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const serializedFilters = computed(() => {
      return {
        model: 'detailed',
        empNumber: filters.value.employee?.id,
        employeeId: filters.value.employeeId,
        empStatusId: filters.value.empStatusId?.id,
        includeEmployees: filters.value.includeEmployees?.param,
        supervisorEmpNumbers: filters.value.supervisor
          ? [filters.value.supervisor.id]
          : undefined,
        jobTitleId: filters.value.jobTitleId?.id,
        subunitId: filters.value.subunitId?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
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
      includeOpts: [
        {
          id: 1,
          param: 'onlyCurrent',
          label: this.$t('general.current_employees_only'),
        },
        {
          id: 2,
          param: 'currentAndPast',
          label: this.$t('general.current_and_past_employees'),
        },
        {
          id: 3,
          param: 'onlyPast',
          label: this.$t('general.past_employees_only'),
        },
      ],
      checkedItems: [],
    };
  },
  computed: {
    headers() {
      return [
        {
          name: 'employeeId',
          slot: 'title',
          title: this.$t('general.id'),
          sortField: 'employee.employeeId',
          style: {flex: 1},
        },
        {
          name: 'firstAndMiddleName',
          title: this.$t('pim.first_middle_name'),
          sortField: 'employee.firstName',
          style: {flex: 1},
        },
        {
          name: 'lastName',
          title: this.$t('general.last_name'),
          sortField: 'employee.lastName',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: this.$t('general.job_title'),
          sortField: 'jobTitle.jobTitleName',
          style: {flex: 1},
        },
        {
          name: 'empStatus',
          title: this.$t('general.employment_status'),
          sortField: 'empStatus.name',
          style: {flex: 1},
        },
        {
          name: 'subunit',
          title: this.$t('general.sub_unit'),
          sortField: 'subunit.name',
          style: {flex: 1},
        },
        {
          name: 'supervisor',
          title: this.$t('pim.supervisor'),
          sortField: 'supervisor.firstName',
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            ...(this.$can.delete('employee_list') && {
              delete: {
                onClick: this.onClickDelete,
                component: 'oxd-icon-button',
                props: {
                  name: 'trash',
                },
              },
            }),
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ];
    },
  },

  methods: {
    onClickAdd() {
      navigate('/pim/addEmployee');
    },
    onClickEdit($event) {
      const id = $event.id ? $event.id : $event.item?.id;
      navigate('/pim/viewPersonalDetails/empNumber/{id}', {id});
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
    onClickDelete(item, $event) {
      $event.stopImmediatePropagation();
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
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
