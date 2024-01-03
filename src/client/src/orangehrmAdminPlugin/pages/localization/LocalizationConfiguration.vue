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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('admin.localization') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.language"
                :label="$t('general.language')"
                type="select"
                :show-empty-selector="false"
                :options="languageList"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.dateFormat"
                :label="$t('admin.date_format')"
                type="select"
                :show-empty-selector="false"
                :options="dateFormatList"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-actions>
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {reloadPage} from '@ohrm/core/util/helper/navigation';
export default {
  props: {
    dateFormatList: {
      type: Array,
      required: true,
    },
    languageList: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/localization',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      configuration: {
        language: null,
        dateFormat: null,
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        this.configuration.language = this.languageList.find(
          (item) => item.id === data.language,
        );
        this.configuration.dateFormat = this.dateFormatList.find(
          (item) => item.id === data.dateFormat,
        );
      })
      .finally(() => (this.isLoading = false));
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: '/api/v2/admin/localization',
          data: {
            language: this.configuration.language?.id,
            dateFormat: this.configuration.dateFormat?.id,
          },
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => reloadPage());
    },
  },
};
</script>
