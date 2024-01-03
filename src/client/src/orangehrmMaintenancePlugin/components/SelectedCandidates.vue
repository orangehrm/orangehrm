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
  <purge-candidate-records @search="onClickSearch" />
  <div v-if="vacancy" class="orangehrm-paper-container">
    <div v-show="total > 0" class="orangehrm-header-container">
      <oxd-button
        :label="$t('maintenance.purge_all')"
        display-type="secondary"
        @click="onClickPurge"
      />
    </div>
    <table-header
      :total="total"
      :selected="0"
      :show-divider="total > 0"
      :loading="isLoading || loading"
    ></table-header>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :clickable="false"
        :selectable="false"
        :loading="isLoading || loading"
        :items="items.data"
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
</template>

<script>
import {computed, ref} from 'vue';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import CandidateRecords from '@/orangehrmMaintenancePlugin/components/CandidateRecords';

export default {
  name: 'SelectedCandidates',

  components: {
    'purge-candidate-records': CandidateRecords,
  },

  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['purge'],

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/maintenance/candidates',
    );
    const vacancy = ref(null);
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();

    const serializedFilters = computed(() => {
      return {
        vacancyId: vacancy.value,
      };
    });

    const purgeCandidateNormalizer = (data) => {
      return data.map((item) => {
        return {
          name: $tEmpName(
            {
              firstName: item.firstName,
              middleName: item.middleName,
              lastName: item.lastName,
              terminationId: null,
            },
            {includeMiddle: true},
          ),
          date: formatDate(parseDate(item.dateOfApplication), jsDateFormat, {
            locale,
          }),
          status: item.status.label,
        };
      });
    };

    const {
      total,
      pages,
      response,
      isLoading,
      currentPage,
      showPaginator,
      execQuery,
    } = usePaginate(http, {
      prefetch: false,
      query: serializedFilters,
      normalizer: purgeCandidateNormalizer,
    });

    return {
      http,
      total,
      pages,
      vacancy,
      isLoading,
      currentPage,
      showPaginator,
      items: response,
      execQuery,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'name',
          title: this.$t('recruitment.candidate_name'),
          style: {flex: '45%'},
        },
        {
          name: 'date',
          title: this.$t('recruitment.date_of_application'),
          style: {flex: '45%'},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          style: {flex: '10%'},
        },
      ],
    };
  },

  methods: {
    onClickSearch(vacancy) {
      this.vacancy = vacancy;
      this.execQuery();
    },
    onClickPurge() {
      if (this.vacancy) this.$emit('purge', this.vacancy);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-paper-container {
  margin-top: 1rem;
}
</style>
