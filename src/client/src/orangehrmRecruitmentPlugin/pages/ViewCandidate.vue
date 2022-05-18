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
  <div class="orangehrm-candidate-page">
    <oxd-table-filter :filter-title="$t('general.candidates')">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <jobtitle-dropdown v-model="filters.jobTitle"></jobtitle-dropdown>
            </oxd-grid-item>
            <oxd-grid-item>
              <vacancy-dropdown v-model="filters.vacancy"></vacancy-dropdown>
            </oxd-grid-item>
            <oxd-grid-item>
              <hiring-manager-dropdown
                v-model="filters.hiringManager"
              ></hiring-manager-dropdown>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.status"
                type="select"
                :label="$t('general.status')"
                :options="statuses"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <candidate-autocomplete v-model="filters.candidate" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.keywords"
                :label="$t('recruitment.keywords')"
                :placeholder="$t('recruitment.enter_comma_se')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.fromDate"
                type="date"
                :label="$t('recruitment.date_of_application')"
                :placeholder="$t('general.from')"
                :rules="rules.fromDate"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-candidate-page-date">
              <oxd-input-field
                v-model="filters.toDate"
                type="date"
                :placeholder="$t('general.to')"
                :rules="rules.toDate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.methodOfApplication"
                :label="$t('recruitment.method_of_application')"
                type="select"
                :options="applications"
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
import useSort from '@ohrm/core/util/composable/useSort';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown';
import HiringManagerDropdown from '@/orangehrmRecruitmentPlugin/components/HiringManagerDropdown';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import usei18n from '@/core/util/composable/usei18n';

import {
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
  validDateFormat,
} from '@/core/util/validation/rules';
import CandidateAutocomplete from '@/orangehrmRecruitmentPlugin/components/CandidateAutocomplete';
const defaultFilters = {
  jobTitle: null,
  vacancy: null,
  hiringManager: null,
  status: null,
  keywords: null,
  application: null,
  candidate: '',
  fromDate: null,
  toDate: null,
};
const defaultSortOrder = {
  'vacancy.name': 'DEFAULT',
  'candidate.lastName': 'DEFAULT',
  'employee.lastName': 'DEFAULT',
  'candidate.dateOfApplication': 'DESC',
  'candidateVacancy.status': 'DEFAULT',
};

export default {
  name: 'ViewCandidate',
  components: {
    CandidateAutocomplete,
    'delete-confirmation': DeleteConfirmationDialog,
    'candidate-autocomplete': CandidateAutocomplete,
    'hiring-manager-dropdown': HiringManagerDropdown,
    'vacancy-dropdown': VacancyDropdown,
    'jobtitle-dropdown': JobtitleDropdown,
  },

  setup() {
    const {$t} = usei18n();

    const statuses = [
      {
        id: 1,
        label: $t('recruitment.application_initiated'),
      },
      {
        id: 2,
        label: $t('recruitment.shortlisted'),
      },
      {
        id: 3,
        label: $t('recruitment.interview_scheduled'),
      },
      {
        id: 4,
        label: $t('recruitment.interview_passed'),
      },
      {
        id: 5,
        label: $t('recruitment.interview_failed'),
      },
      {
        id: 6,
        label: $t('recruitment.job_offered'),
      },
      {
        id: 7,
        label: $t('recruitment.offered_declined'),
      },
    ];
    const candidateDataNormalizer = data => {
      return data.map(item => {
        return {
          id: item.id,
          vacancy: item.vacancy?.name,
          candidate: `${item.firstName} ${item.middleName} ${item.lastName}`,
          manager: item.vacancy
            ? `${item.vacancy.hiringManager.firstName} ${item.vacancy.hiringManager.middleName} ${item.vacancy.hiringManager.lastName}`
            : '',
          dateOfApplication: item.dateOfApplication,
          status: item.status?.label,
          resume: item.hasAttachment,
        };
      });
    };
    const filters = ref({...defaultFilters});
    const rules = {
      fromDate: [
        validDateFormat(),
        startDateShouldBeBeforeEndDate(
          () => filters.value.toDate,
          $t('general.from_date_should_be_before_to_date'),
          {allowSameDate: true},
        ),
      ],
      toDate: [
        validDateFormat(),
        endDateShouldBeAfterStartDate(
          () => filters.value.fromDate,
          $t('general.to_date_should_be_after_from_date'),
          {allowSameDate: true},
        ),
      ],
    };
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const serializedFilters = computed(() => {
      return {
        jobTitleId: filters.value.jobTitle?.id,
        vacancyId: filters.value.vacancy?.id,
        hiringManagerId: filters.value.hiringManager?.id,
        keywords: filters.value.keywords,
        candidateId: filters.value.candidate?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        status: filters.value.status,
        methodOfApplication: filters.value.methodOfApplication?.id,
        model: 'detailed',
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/candidates',
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
      normalizer: candidateDataNormalizer,
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
      statuses,
      rules,
    };
  },
  data() {
    return {
      checkedItems: [],
      headers: [
        {
          name: 'vacancy',
          title: this.$t('recruitment.vacancy'),
          sortField: 'vacancy.name',
          style: {flex: 1},
        },
        {
          name: 'candidate',
          slot: 'title',
          title: this.$t('recruitment.candidate'),
          sortField: 'candidate.lastName',
          style: {flex: 1},
        },
        {
          name: 'manager',
          title: this.$t('recruitment.hiring_manager'),
          sortField: 'hiringManager.lastName',
          style: {flex: 1},
        },
        {
          name: 'dateOfApplication',
          title: this.$t('recruitment.date_of_application'),
          sortField: 'candidate.dateOfApplication',
          style: {flex: 1},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          sortField: 'candidateVacancy.status',
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
        },
      ],
      applications: [
        {
          id: 1,
          label: this.$t('recruitment.manual'),
        },
        {
          id: 2,
          label: this.$t('recruitment.online'),
        },
      ],
    };
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {
        delete: {
          onClick: this.onClickDelete,
          component: 'oxd-icon-button',
          props: {
            name: 'trash',
          },
        },
        view: {
          onClick: this.onClickEdit,
          props: {
            name: 'eye',
          },
        },
      };

      if (row.resume) {
        cellConfig.download = {
          onClick: this.onDownload,
          props: {
            name: 'download',
          },
        };
      }
      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    onClickAdd() {
      navigate('/recruitment/addCandidate');
    },
    onClickEdit(item) {
      navigate('/recruitment/viewCandidate/{id}', {id: item.id});
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
    onDownload(item) {
      if (!item?.id) return;
      const fileUrl = 'recruitment/viewCandidateAttachment/candidateId';
      const downUrl = `${window.appGlobal.baseUrl}/${fileUrl}/${item.id}`;
      window.open(downUrl, '_blank');
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
          })
          .catch(() => {
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

<style lang="scss" scoped>
.orangehrm-candidate-page {
  &-date {
    .oxd-input-group {
      height: 100%;
      justify-content: center;
    }
  }
}
</style>
