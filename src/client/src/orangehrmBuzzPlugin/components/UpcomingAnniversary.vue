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
  <div v-if="anniversariesCount > 0">
    <oxd-sheet
      :gutters="false"
      type="pastel-white"
      :rounded="false"
      class="orangehrm-buzz-card"
    >
      <oxd-text tag="p" class="orangehrm-buzz-card-title">
        {{ $t('buzz.upcoming_anniversaries') }}
      </oxd-text>
      <div class="orangehrm-buzz-body">
        <div
          v-for="anniversary in filteredAnniversaries"
          :key="anniversary"
          class="orangehrm-buzz-anniversary"
        >
          <div class="orangehrm-buzz-anniversary-profile">
            <div class="orangehrm-buzz-anniversary-profile-image">
              <img
                alt="profile picture"
                class="employee-image"
                :src="`../pim/viewPhoto/empNumber/${anniversary.empNumber}`"
              />
            </div>
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
              <oxd-text
                tag="p"
                class="orangehrm-buzz-anniversary-duration-years"
              >
                {{ anniversary.anniversaryYear }} <br />
                {{
                  $t('buzz.n_year', {yearsCount: anniversary.anniversaryYear})
                }}
              </oxd-text>
              <oxd-text
                tag="p"
                class="orangehrm-buzz-anniversary-duration-date"
              >
                {{ anniversary.joinedDate }}
              </oxd-text>
            </div>
          </div>
        </div>
      </div>
      <div v-if="anniversariesCount > 5">
        <oxd-text
          tag="p"
          class="orangehrm-buzz-anniversary-see-more"
          @click="onSeeMore"
        >
          {{
            isViewDetails ? $t('general.show_more') : $t('general.show_less')
          }}
        </oxd-text>
      </div>
    </oxd-sheet>
  </div>
</template>
<script>
import useLocale from '@/core/util/composable/useLocale';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import {APIService} from '@/core/util/services/api.service';
import {parseDate, formatDate} from '@/core/util/helper/datefns';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'UpcomingAnniversaries',

  components: {
    'oxd-sheet': Sheet,
  },

  setup() {
    const {locale} = useLocale();
    const {$tEmpName} = useEmployeeNameTranslate();
    const celebrationPic = `${window.appGlobal.baseUrl}/../dist/img/year_celebration.png`;

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/buzz/anniversaries',
    );

    return {
      http,
      locale,
      celebrationPic,
      tEmpName: $tEmpName,
    };
  },

  data() {
    return {
      viewMore: false,
      anniversaries: [],
    };
  },

  computed: {
    isViewDetails() {
      return !this.viewMore;
    },
    filteredAnniversaries() {
      return this.anniversaries.slice(
        0,
        this.viewMore ? this.anniversaries.length - 1 : 5,
      );
    },
    anniversariesCount() {
      return this.anniversaries.length;
    },
  },

  beforeMount() {
    this.http.getAll().then(response => {
      const {data} = response.data;
      this.anniversaries = data.map(item => {
        const {employee, jobTitle, joinedDate} = item;
        return {
          empNumber: employee.empNumber,
          empName: this.tEmpName(employee, {
            includeMiddle: false,
            excludePastEmpTag: false,
          }),
          jobTitle: jobTitle,
          joinedDate: formatDate(parseDate(joinedDate), 'MMM dd', {
            locale: this.locale,
          }),
          anniversaryYear:
            new Date().getFullYear() - new Date(joinedDate).getFullYear(),
        };
      });
    });
  },

  methods: {
    onSeeMore() {
      this.viewMore = !this.viewMore;
    },
  },
};
</script>
<style src="./upcomming-anniversary.scss" lang="scss" scoped></style>
