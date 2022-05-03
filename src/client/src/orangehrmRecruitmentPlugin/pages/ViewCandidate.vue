<template>
  <div class="orangehrm-candidate-page">
    <oxd-table-filter filter-title="Candidates">
      <oxd-form>
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
                :label="'Status'"
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
                :label="'Keywords'"
                placeholder="Enter comma se"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.dateFrom"
                type="date"
                :label="'Date of Application'"
                placeholder="From"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-candidate-page-date">
              <oxd-input-field
                v-model="filters.dateTo"
                type="date"
                placeholder="To"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.application"
                :label="'Method of Application'"
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
import CandidateAutocomplete from '@/orangehrmRecruitmentPlugin/components/CandidateAutocomplete';
const defaultFilters = {
  jobTitle: null,
  vacancy: null,
  hiringManager: null,
  status: null,
  keywords: null,
  application: null,
  candidate: '',
  dateFrom: '',
  dateTo: '',
};
const defaultSortOrder = {
  'v.vacancy': 'ASC',
  'c.candidate': 'ASC',
  'h.manager': 'ASC',
  's.status': 'DEFAULT',
};

export default {
  name: 'ViewCandidate',
  components: {
    CandidateAutocomplete,
    HiringManagerDropdown,
    VacancyDropdown,
    JobtitleDropdown,
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
          vacancy: item.vacancy,
          candidate: item.candidate,
          manager: item.hiringManager,
          status: item.status,
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
        jobTitle: filters.value.jobTitle?.id,
        vacancy: filters.value.vacancy?.id,
        manager: filters.value.hiringManager?.id,
        keywords: filters.value.keywords?.id,
        application: filters.value.application?.id,
        candidate: filters.value.candidate?.id,
        dateFrom: filters.value.dateFrom,
        dateTo: filters.value.dateTo,
        status: filters.value.status,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      'recruitment/api/candidateDetails',
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
      checkedItems: [],
      headers: [
        {
          name: 'vacancy',
          title: 'Vacancy',
          sortField: 'v.vacancy',
          style: {flex: 1},
        },
        {
          name: 'candidate',
          title: 'Candidate',
          sortField: 'c.candidate',
          style: {flex: 1},
        },
        {
          name: 'manager',
          slot: 'title',
          title: 'Hiring Manager',
          sortField: 'h.manager',
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
      statuses: [
        {
          id: 1,
          label: 'Application initiated',
        },
        {
          id: 2,
          label: 'Shortlisted',
        },
        {
          id: 3,
          label: 'Interview Scheduled',
        },
        {
          id: 4,
          label: 'Interview Passed',
        },
        {
          id: 5,
          label: 'Interview Failed',
        },
        {
          id: 6,
          label: 'Job Offered',
        },
        {
          id: 7,
          label: 'offered Declined',
        },
        {
          id: 7,
          label: 'offered Declined',
        },
      ],
      applications: [
        {
          id: 1,
          label: 'Manual',
        },
        {
          id: 2,
          label: 'Online',
        },
      ],
    };
  },
  methods: {
    onClickAdd() {
      navigate('/admin/saveCandidate');
    },
    onClickEdit(item) {
      navigate('/admin/saveCandidate/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.items?.data[index].id;
      });
      // this.$refs.deleteDialog.showDialog().then(confirmation => {
      //   if (confirmation === 'ok') {
      //     this.deleteItems(ids);
      //   }
      // });
    },
    onClickDelete(item) {
      const isSelectable = this.unselectableIds.findIndex(id => id == item.id);
      // if (isSelectable > -1) {
      //   return this.$toast.cannotDelete();
      // }
      // this.$refs.deleteDialog.showDialog().then(confirmation => {
      //   if (confirmation === 'ok') {
      //     this.deleteItems([item.id]);
      //   }
      // });
    },
    deleteItems(items) {
      if (items instanceof Array) {
        // this.isLoading = true;
        // this.http
        //   .deleteAll({
        //     ids: items,
        //   })
        //   .then(() => {
        //     return this.$toast.deleteSuccess();
        //   })
        //   .then(() => {
        //     this.isLoading = false;
        //     this.resetDataTable();
        //   });
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
