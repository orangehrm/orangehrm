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
    <oxd-grid class="orangehrm-container">
      <oxd-grid-item v-for="vacancy in vacancies?.data" :key="vacancy">
        <vacancy-card
          :vacancy-description="vacancy.vacancyDescription"
          :vacancy-id="vacancy.vacancyId"
          :vacancy-title="vacancy.vacancyTitle"
        ></vacancy-card>
      </oxd-grid-item>
      <oxd-loading-spinner
        v-if="isLoading"
        class="orangehrm-container-loader"
      />
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          v-model:current="currentPage"
          :length="pages"
        />
      </div>
    </oxd-grid>
  </div>
  <div>
    <div class="orangehrm-container-img-div">
      <oxd-text type="toast-message">
        {{ $t('recruitment.powered_by') }}
      </oxd-text>
      <img
        :src="defaultPic"
        alt="OrangeHRM Picture"
        class="orangehrm-container-img"
      />
    </div>
    <div>
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script>
import VacancyCard from '@/orangehrmRecruitmentPlugin/components/VacancyCard';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import usePaginate from '@/core/util/composable/usePaginate';

export default {
  name: 'VacancyList',
  components: {
    'vacancy-card': VacancyCard,
    'oxd-loading-spinner': Spinner,
  },
  setup() {
    const defaultPic = `${window.appGlobal.baseUrl}/../images/logo.png`;
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
      '/api/v2/recruitment/public/vacancies',
    );
    const {
      showPaginator,
      currentPage,
      total,
      pages,
      response,
      isLoading,
    } = usePaginate(http, {
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

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm {
  &-background-container {
    height: 80%;
  }

  &-container {
    height: 100%;
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
    &-img {
      height: 40px;
    }
    &-img-div {
      display: block;
      padding-left: 48px;
      @include oxd-respond-to('md') {
        margin-bottom: -32px;
        padding-left: 120px;
      }
      @include oxd-respond-to('xl') {
        margin-bottom: -32px;
        padding-left: 120px;
      }
    }
  }
}
</style>
