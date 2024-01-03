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
  <oxd-dialog
    :style="{width: '90%', maxWidth: '450px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('general.about') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <div v-if="isLoading" class="orangehrm-loader">
      <oxd-loading-spinner />
    </div>
    <oxd-grid v-else :cols="2" class="orangehrm-about">
      <oxd-grid-item>
        <oxd-text tag="p" class="orangehrm-about-title">
          {{ $t('general.company_name') }}:
        </oxd-text>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text tag="p" class="orangehrm-about-text">
          {{ data.companyName }}
        </oxd-text>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text tag="p" class="orangehrm-about-title">
          {{ $t('general.version') }}:
        </oxd-text>
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-text tag="p" class="orangehrm-about-text">
          {{ data.productName }} {{ data.version }}
        </oxd-text>
      </oxd-grid-item>
      <template v-if="data.numberOfActiveEmployee !== undefined">
        <oxd-grid-item>
          <oxd-text tag="p" class="orangehrm-about-title">
            {{ $t('general.active_employees') }}:
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-text tag="p" class="orangehrm-about-text">
            {{ data.numberOfActiveEmployee }}
          </oxd-text>
        </oxd-grid-item>
      </template>
      <template v-if="data.numberOfPastEmployee !== undefined">
        <oxd-grid-item>
          <oxd-text tag="p" class="orangehrm-about-title">
            {{ $t('general.employees_terminated') }}:
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-text tag="p" class="orangehrm-about-text">
            {{ data.numberOfPastEmployee }}
          </oxd-text>
        </oxd-grid-item>
      </template>
    </oxd-grid>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {OxdDialog, OxdSpinner} from '@ohrm/oxd';

export default {
  components: {
    'oxd-loading-spinner': OxdSpinner,
    'oxd-dialog': OxdDialog,
  },
  emits: ['close'],
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/core/about');
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      data: null,
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        this.data = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-loader {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80px;
}
.orangehrm-about {
  grid-template-columns: 150px 1fr;
  &-title,
  &-text {
    word-break: break-word;
    font-size: $oxd-input-control-font-size;
  }
  &-title {
    font-weight: 700;
  }
}
</style>
