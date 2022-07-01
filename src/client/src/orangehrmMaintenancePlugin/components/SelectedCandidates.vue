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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" :class="classes">
        {{ $t('maintenance.selected_candidates') }}
      </oxd-text>
      <oxd-button
        v-show="total > 0"
        :label="$t('maintenance.purge_all')"
        display-type="secondary"
        @click="$emit('purge')"
      />
    </div>
    <table-header :total="total" :loading="isLoading || loading"></table-header>
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
import {computed} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'SelectedCandidates',
  props: {
    vacancyId: {
      type: Number,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['purge'],
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/maintenance/candidates',
    );

    const {$tEmpName} = useEmployeeNameTranslate();
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const serializedFilters = computed(() => {
      return {
        vacancyId: props.vacancyId,
      };
    });
    const purgeCandidateNormalizer = data => {
      return data.map(item => {
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
          style: {flex: 1},
        },
        {
          name: 'date',
          title: this.$t('recruitment.date_of_application'),
          style: {flex: 1},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          style: {flex: 1},
        },
      ],
    };
  },
  computed: {
    classes() {
      return {
        'orangehrm-main-title': true,
        'orangehrm-selected-candidate-text': this.total > 0,
      };
    },
  },
  watch: {
    vacancyId(value) {
      if (value !== null) {
        this.execQuery();
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-header-container {
  flex-direction: column;
  align-items: flex-start;
}
.orangehrm-selected-candidate-text {
  padding-bottom: 0.6rem;
}
</style>
