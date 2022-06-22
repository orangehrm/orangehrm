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
    <oxd-grid ref="scrollerRef" class="orangehrm-container">
      <oxd-grid-item v-for="vacancy in vacancies" :key="vacancy">
        <vacancy-card
          :vacancy-id="vacancy.vacancyId"
          :vacancy-description="vacancy.vacancyDescription"
          :vacancy-title="vacancy.vacancyTitle"
        ></vacancy-card>
      </oxd-grid-item>
      <oxd-loading-spinner
        v-if="isLoading"
        class="orangehrm-container-loader"
      />
    </oxd-grid>
  </div>
  <div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import VacancyCard from '@/orangehrmRecruitmentPlugin/components/VacancyCard';
import {APIService} from '@/core/util/services/api.service';
import useInfiniteScroll from '@ohrm/core/util/composable/useInfiniteScroll';
import {reactive, toRefs} from 'vue';
import useToast from '@/core/util/composable/useToast';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';

export default {
  name: 'VacancyList',
  components: {
    'vacancy-card': VacancyCard,
    'oxd-loading-spinner': Spinner,
  },
  setup() {
    const {noRecordsFound} = useToast();
    const vacancyDataNormalizer = data => {
      return data.map(item => {
        return {
          vacancyId: item.id,
          vacancyTitle: item.name,
          vacancyDescription: item.description,
        };
      });
    };
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/fetch-vacancy-list',
    );
    const state = reactive({
      vacancies: [],
      total: 0,
      limit: 8,
      offset: 0,
      isLoading: false,
    });
    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: state.limit,
          offset: state.offset,
        })
        .then(response => {
          const {data, meta} = response.data;
          state.total = meta?.total || 0;
          if (Array.isArray(data)) {
            state.vacancies = [
              ...state.vacancies,
              ...vacancyDataNormalizer(data),
            ];
          }
          if (state.total === 0) {
            noRecordsFound();
          }
        })
        .finally(() => (state.isLoading = false));
    };

    const {scrollerRef} = useInfiniteScroll(() => {
      if (state.vacancies.length >= state.total) return;
      state.offset += state.limit;
      fetchData();
    });

    return {
      scrollerRef,
      ...toRefs(state),
      fetchData,
    };
  },
  beforeMount() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm {
  &-container {
    height: 512px;
    @include oxd-scrollbar();
    overflow: auto;
    position: relative;
    margin: 0;

    &-loader {
      margin: 0 auto;
      background-color: $oxd-white-color;
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      bottom: 0;
    }
  }
}
</style>
