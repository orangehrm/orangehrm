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
    <oxd-table-filter :filter-title="$t('general.projects')">
      <oxd-form @submit-valid="filterItems" @reset="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <customer-autocomplete
                v-model="filters.customer"
                :rules="rules.customer"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <project-autocomplete
                v-model="filters.project"
                :exclude-customer-name="true"
                :rules="rules.project"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <project-admin-autocomplete
                v-model="filters.projectAdmin"
                :show-delete="false"
                :rules="rules.projectAdmin"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="reset"
            display-type="ghost"
            :label="$t('general.reset')"
          />
          <submit-button :label="$t('general.search')" />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>

    <br />
    <div class="orangehrm-paper-container">
      <div
        v-if="$can.create(`time_projects`)"
        class="orangehrm-header-container"
      >
        <oxd-button
          icon-name="plus"
          display-type="secondary"
          :label="$t('general.add')"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :loading="isLoading"
        :total="total"
        :show-divider="$can.create(`time_projects`)"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
          :clickable="false"
          :loading="isLoading"
          row-decorator="oxd-table-decorator-card"
          :selectable="$can.delete(`time_projects`)"
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
import usei18n from '@/core/util/composable/usei18n';
import {validSelection} from '@/core/util/validation/rules';
import useSort from '@ohrm/core/util/composable/useSort';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';
import CustomerAutocomplete from '@/orangehrmTimePlugin/components/CustomerAutocomplete.vue';
import ProjectAdminAutocomplete from '@/orangehrmTimePlugin/components/ProjectAdminAutocomplete.vue';

const defaultFilters = {
  customer: null,
  project: null,
  projectAdmin: null,
};

const defaultSortOrder = {
  'project.name': 'ASC',
  'customer.name': 'DEFAULT',
  'employee.lastName': 'DEFAULT',
};

export default {
  components: {
    'project-autocomplete': ProjectAutocomplete,
    'customer-autocomplete': CustomerAutocomplete,
    'delete-confirmation': DeleteConfirmationDialog,
    'project-admin-autocomplete': ProjectAdminAutocomplete,
  },
  props: {
    unselectableIds: {
      type: Array,
      default: () => [],
    },
  },
  setup(props) {
    const {$t} = usei18n();
    const {$tEmpName} = useEmployeeNameTranslate();

    const projectNormalizer = (data) => {
      return data.map((item) => {
        const selectable = props.unselectableIds.findIndex(
          (id) => id == item.id,
        );
        return {
          id: item.id,
          project: item.name,
          customer: item.customer?.deleted
            ? item.customer?.name + $t('general.deleted')
            : item.customer?.name,
          projectAdmins: item.projectAdmins
            ?.map((projectAdmin) => $tEmpName(projectAdmin))
            .join(', '),
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
        customerId: filters.value.customer?.id,
        projectId: filters.value.project?.id,
        empNumber: filters.value.projectAdmin?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        model: 'detailed',
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/projects',
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
      normalizer: projectNormalizer,
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
      checkedItems: [],
      rules: {
        project: [validSelection],
        customer: [validSelection],
        projectAdmin: [validSelection],
      },
    };
  },
  computed: {
    headers() {
      const headers = [
        {
          name: 'customer',
          title: this.$t('time.customer_name'),
          sortField: 'customer.name',
          style: {flex: '15%'},
        },
        {
          name: 'project',
          slot: 'title',
          title: this.$t('time.project'),
          sortField: 'project.name',
          style: {flex: '15%'},
        },
        {
          name: 'projectAdmins',
          title: this.$t('time.project_admins'),
          style: {flex: '20%'},
        },
      ];
      const headerActions = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {},
      };
      if (this.$can.delete(`time_projects`)) {
        headerActions.cellConfig.delete = {
          onClick: this.onClickDelete,
          props: {
            name: 'trash',
          },
        };
      }
      if (this.$can.update(`time_project_activities`)) {
        headerActions.cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
      }
      if (Object.keys(headerActions.cellConfig).length > 0) {
        headers.push(headerActions);
      }
      return headers;
    },
  },
  methods: {
    onClickAdd() {
      navigate('/time/saveProject');
    },
    onClickEdit(item) {
      navigate('/time/saveProject/{id}', {id: item.id});
    },
    onClickDelete(item) {
      const isSelectable = this.unselectableIds.findIndex(
        (id) => id == item.id,
      );
      if (isSelectable > -1) {
        return this.$toast.error({
          title: this.$t('general.error'),
          message: this.$t(
            'time.not_allowed_to_delete_projects_which_have_time_logged',
          ),
        });
      }
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteData([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map((index) => {
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
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
  },
};
</script>
