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
  <div class="orangehrm-buzz-anniversary">
    <oxd-text type="card-title" class="orangehrm-buzz-anniversary-title">
      {{ $t('buzz.upcoming_anniversaries') }}
    </oxd-text>
    <div
      class="orangehrm-buzz-anniversary-content"
      :class="{'--show-more': anniversariesCount > 5}"
    >
      <div
        v-for="anniversary in anniversaries"
        :key="anniversary"
        class="orangehrm-buzz-anniversary-item"
      >
        <div class="orangehrm-buzz-anniversary-profile">
          <profile-image :employee="anniversary"></profile-image>
          <div class="orangehrm-buzz-anniversary-profile-details">
            <oxd-text tag="p" class="orangehrm-buzz-anniversary-emp-name">
              {{ anniversary.empName }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-anniversary-job-details">
              {{ anniversary.jobTitle }}
            </oxd-text>
          </div>
        </div>
        <div class="orangehrm-buzz-anniversary-duration">
          <img
            alt="year celebration"
            class="orangehrm-buzz-anniversary-year-celebration"
            :src="celebrationPic"
          />
          <div class="orangehrm-buzz-anniversary-durations-text">
            <oxd-text tag="p" class="orangehrm-buzz-anniversary-duration-years">
              {{ anniversary.anniversaryYear }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-anniversary-duration-years">
              {{ $t('buzz.n_year', {yearsCount: anniversary.anniversaryYear}) }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-buzz-anniversary-duration-date">
              {{ anniversary.joinedDate }}
            </oxd-text>
          </div>
        </div>
      </div>
      <div v-if="isEmpty" class="orangehrm-buzz-anniversary-nocontent">
        <img :src="noContentPic" alt="No Content" />
        <oxd-text tag="p">
          {{ $t('general.no_records_found') }}
        </oxd-text>
      </div>
    </div>
    <div
      v-if="anniversariesCount > 5"
      class="orangehrm-buzz-anniversary-footer"
    >
      <oxd-text tag="p" @click="onSeeMore">
        {{ isViewDetails ? $t('general.show_more') : $t('general.show_less') }}
      </oxd-text>
    </div>
  </div>
</template>

<script>
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import {parseDate, formatDate} from '@/core/util/helper/datefns';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'UpcomingAnniversaries',

  components: {
    'profile-image': ProfileImage,
  },

  setup() {
    const {locale} = useLocale();
    const {$tEmpName} = useEmployeeNameTranslate();
    const celebrationPic = `${window.appGlobal.publicPath}/images/year_celebration.png`;
    const noContentPic = `${window.appGlobal.publicPath}/images/buzz_no_anniversaries.png`;

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/buzz/anniversaries',
    );

    return {
      http,
      locale,
      noContentPic,
      celebrationPic,
      tEmpName: $tEmpName,
    };
  },

  data() {
    return {
      viewMore: false,
      isLoading: false,
      anniversaries: [],
      anniversariesCount: 0,
    };
  },

  computed: {
    isViewDetails() {
      return !this.viewMore;
    },
    isEmpty() {
      return !this.isLoading && this.anniversaries.length === 0;
    },
  },

  beforeMount() {
    this.anniversariesLimit = 5;
    this.getAnniversaries();
  },

  methods: {
    onSeeMore() {
      this.viewMore = !this.viewMore;
      if (this.viewMore) {
        this.anniversariesLimit = 0;
      } else {
        this.anniversariesLimit = 5;
      }
      this.getAnniversaries();
    },
    getAnniversaries() {
      this.isLoading = true;
      this.http
        .getAll({limit: this.anniversariesLimit})
        .then((response) => {
          const {data, meta} = response.data;
          this.anniversaries = data.map((item) => {
            const {employee, jobTitle, joinedDate} = item;
            return {
              empNumber: employee.empNumber,
              empName: this.tEmpName(employee, {
                includeMiddle: false,
                excludePastEmpTag: false,
              }),
              jobTitle: jobTitle.title,
              joinedDate: formatDate(parseDate(joinedDate), 'MMM dd', {
                locale: this.locale,
              }),
              anniversaryYear:
                new Date().getFullYear() - new Date(joinedDate).getFullYear(),
            };
          });
          this.anniversariesCount = meta?.total;
        })
        .finally(() => (this.isLoading = false));
    },
  },
};
</script>

<style src="./upcoming-anniversaries.scss" lang="scss" scoped></style>
