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
  <div class="orangehrm-card-container">
    <oxd-form :loading="isLoading">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('recruitment.application_stage') }}
      </oxd-text>
      <oxd-divider />
      <oxd-grid :cols="3" class="orangehrm-full-width-grid">
        <oxd-grid-item>
          <oxd-input-group :label="$t('general.name')">
            <oxd-text tag="p">
              {{ candidateName }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group :label="$t('recruitment.vacancy')">
            <oxd-text tag="p">
              {{ vacancyName }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group :label="$t('recruitment.hiring_manager')">
            <oxd-text tag="p">
              {{ hiringManagerName }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
      </oxd-grid>
      <oxd-divider />
      <slot name="footer"></slot>
    </oxd-form>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
export default {
  name: 'RecruitmentStatus',
  props: {
    candidateId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/recruitment/candidates',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      candidateName: '',
      vacancyName: 'N/A',
      hiringManagerName: '',
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http.get(this.candidateId).then(response => {
      const {data} = response.data;
      this.candidateName = `${data?.firstName} ${data?.middleName} ${data?.lastName}`;
      if (data?.vacancy) {
        this.vacancyName = data?.vacancy.name;
        this.hiringManagerName = `${data?.vacancy.hiringManager.firstName} ${
          data?.vacancy.hiringManager.lastName
        } ${
          data?.vacancy.hiringManager.terminationId
            ? this.$t('general.past_employee')
            : ''
        }`;
      }
      this.isLoading = false;
    });
  },
};
</script>
