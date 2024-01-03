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
    <oxd-table-filter :filter-title="$t('general.vacancies')">
      <oxd-form @submit-valid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <jobtitle-dropdown v-model="filters.jobTitleId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <vacancy-dropdown
                v-model="filters.vacancyId"
                :label="$t('recruitment.vacancy')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <hiring-manager-dropdown v-model="filters.hiringManagerId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.status"
                type="select"
                :label="$t('general.status')"
                :clear="false"
                :options="statusOptions"
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
        :loading="isLoading"
        :total="total"
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
          class="orangehrm-vacancy-list"
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
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@ohrm/core/util/composable/useSort';
import usei18n from '@/core/util/composable/usei18n';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown.vue';
import HiringManagerDropdown from '@/orangehrmRecruitmentPlugin/components/HiringManagerDropdown';

const defaultFilters = {
  jobTitleId: null,
  hiringManagerId: null,
  vacancyId: null,
  status: null,
};
const defaultSortOrder = {
  'vacancy.name': 'ASC',
  'vacancy.status': 'DEFAULT',
  'jobTitle.jobTitleName': 'DEFAULT',
  'hiringManager.lastName': 'DEFAULT',
};
export default {
  name: 'ViewJobVacancy',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'jobtitle-dropdown': JobtitleDropdown,
    'vacancy-dropdown': VacancyDropdown,
    'hiring-manager-dropdown': HiringManagerDropdown,
  },

  setup() {
    const {$t} = usei18n();
    const {$tEmpName} = useEmployeeNameTranslate();
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
        model: 'detailed',
      };
    });

    const userdataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          vacancy: item.name,
          jobTitle: item.jobTitle?.isDeleted
            ? item.jobTitle.title + $t('general.deleted')
            : item.jobTitle?.title,

          hiringManager: item.hiringManager?.id
            ? $tEmpName(item.hiringManager)
            : $t('general.deleted'),
          status: item.status ? $t('general.active') : $t('general.closed'),
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/vacancies',
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
      headers: [
        {
          name: 'vacancy',
          slot: 'title',
          title: this.$t('recruitment.vacancy'),
          sortField: 'vacancy.name',
          style: {flex: 3},
        },
        {
          name: 'jobTitle',
          title: this.$t('general.job_title'),
          sortField: 'jobTitle.jobTitleName',
          style: {flex: 3},
        },
        {
          name: 'hiringManager',
          title: this.$t('recruitment.hiring_manager'),
          sortField: 'hiringManager.lastName',
          style: {flex: 3},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          sortField: 'vacancy.status',
          style: {flex: 2},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 2},
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
        {id: true, param: 'active', label: this.$t('general.active')},
        {id: false, param: 'closed', label: this.$t('general.closed')},
      ],
      vacancies: [],
      checkedItems: [],
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
    onClickReset() {
      this.filters = {...defaultFilters};
      this.filterItems();
    },
  },
};
</script>

<style src="./vacancy.scss" lang="scss" scoped></style>
