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
  <oxd-form :loading="isLoading">
    <div class="orangehrm-card-container">
      <slot name="header-title"></slot>
      <oxd-divider />
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-group :label="$t('general.name')">
              <oxd-text tag="p">
                {{ candidate }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-group :label="$t('recruitment.vacancy')">
              <oxd-text tag="p">
                {{ vacancy }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-group :label="$t('recruitment.hiring_manager')">
              <oxd-text tag="p">
                {{ hiringManager }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-divider />
      <slot name="footer-options"></slot>
    </div>
  </oxd-form>
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
  setup(props) {
    const http = new APIService(
      'https://c81c3149-4936-41d9-ab3d-e25f1bff2934.mock.pstmn.io',
      `/recruitment/status/${props.candidateId}`,
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      candidate: '',
      hiringManager: '',
      vacancy: '',
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then(({data: {data}}) => {
      const {candidate, vacancy, manager} = data;
      this.candidate = candidate;
      this.vacancy = vacancy;
      this.hiringManager =
        (manager.terminationId ? '(Past Employee)' : '') +
        `${manager.firstName} ${manager.middleName} ${manager.lastName}`;
      this.isLoading = false;
    });
  },
};
</script>
