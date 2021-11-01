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
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="vacancies"
          :loading="isLoading"
          :selectable="true"
          :clickable="false"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
    </div>
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

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      vacancy: item.name,
      jobTitle: item.jobTitle?.isDeleted
        ? item.jobTitle.title + ' (Deleted)'
        : item.jobTitle?.title,
      hiringManger: `${item.hiringManager.firstName} ${item.hiringManager.lastName}`,
      status: item.status == 1 ? 'Active' : 'Inactive',
    };
  });
};

const defaultFilters = {
  vacancy: null,
  jobTitleId: null,
  hiringManger: null,
  status: null,
};

const defaultSortOrder = {
  name: 'DEFAULT',
  'employee.firstName': 'ASC',
  'employee.lastName': 'DEFAULT',
  'jobTitle.title': 'DEFAULT',
};
export default {
  name: 'view-job-vacancy',
  components: {
    'jobtitle-dropdown': JobtitleDropdown,
  },

  data() {
    return {
      headers: [
        {
          name: 'vacancy',
          slot: 'Vacancy',
          title: 'Vacancy',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: 'Job Title',
          style: {flex: 1},
        },
        {
          name: 'hiringManger',
          title: 'Hiring Manager',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: 'Status',
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
      http: null,
      vacancies: [],
      checkedItems: [],
      isLoading: false,
    };
  },
  setup() {
    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        model: 'detailed',
        // vacancy: filters.value.vacancy?.id,
        // employeeId: filters.value.employeeId,
        // empStatusId: filters.value.empStatusId?.id,
        // includeEmployees: filters.value.includeEmployees?.param,
        // supervisorEmpNumbers: filters.value.supervisor
        //   ? [filters.value.supervisor.id]
        //   : undefined,
        // jobTitleId: filters.value.jobTitleId?.id,
        // subunitId: filters.value.subunitId?.id,
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

  created() {
    console.log('created');
    const http = this.getConnection();
    this.getAllData();
  },
  methods: {
    onClickAdd() {
      console.log('Add');
      navigate('/recruitment/addJobVacancy');
    },
    getConnection() {
      const connection = new APIService(
        window.appGlobal.baseUrl,
        'api/v2/recruitment/vacancies',
      );
      this.http = connection;
      return connection;
    },
    async getAllData() {
      this.isLoading = true;
      const result = await this.http.getAll();
      this.vacancies = userdataNormalizer(result.data.data);
      this.isLoading = false;
      console.log(this.vacancies);
    },
  },
};
</script>

<style lang="scss" scoped></style>
