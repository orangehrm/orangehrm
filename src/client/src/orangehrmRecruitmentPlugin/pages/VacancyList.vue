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
  <div class="orangehrm-container">
    <vacancy-card
      v-for="vacancy in vacancies?.data"
      :key="vacancy"
      :vacancy-id="vacancy.vacancyId"
      :vacancy-title="vacancy.vacancyTitle"
      :vacancy-description="vacancy.vacancyDescription"
    ></vacancy-card>
    <oxd-loading-spinner v-if="isLoading" class="orangehrm-container-loader" />
    <div v-if="showPaginator" class="orangehrm-bottom-container">
      <oxd-pagination v-model:current="currentPage" :length="pages" />
    </div>
  </div>
  <div class="orangehrm-paper-container">
    <oxd-text tag="p" class="orangehrm-vacancy-list-poweredby">
      {{ $t('recruitment.powered_by') }}
    </oxd-text>
    <img
      :src="defaultPic"
      alt="OrangeHRM Picture"
      class="orangehrm-container-img"
    />
    <slot name="footer"></slot>
  </div>
</template>

<script>
import VacancyCard from '@/orangehrmRecruitmentPlugin/components/VacancyCard';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@/core/util/composable/usePaginate';
import {OxdSpinner} from '@ohrm/oxd';

export default {
  name: 'VacancyList',
  components: {
    'vacancy-card': VacancyCard,
    'oxd-loading-spinner': OxdSpinner,
  },
  setup() {
    const defaultPic = `${window.appGlobal.publicPath}/images/ohrm_branding.png`;
    const vacancyDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          vacancyId: item.id,
          vacancyTitle: item.name,
          vacancyDescription: item.description,
        };
      });
    };
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/public/vacancies',
    );
    const {showPaginator, currentPage, total, pages, response, isLoading} =
      usePaginate(http, {
        normalizer: vacancyDataNormalizer,
        pageSize: 8,
      });
    return {
      defaultPic,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      vacancies: response,
    };
  },
};
</script>

<style src="./public-job-vacancy.scss" lang="scss" scoped></style>
