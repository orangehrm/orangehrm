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
    <div class="orangehrm-card-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">{{
          $t('admin.localization')
        }}</oxd-text>
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
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
                :disabled="true"
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
      'api/v2/admin/localization',
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
      errors: [],
    };
  },
  created() {
    this.isLoading = true;
    this.http.http
      .get('api/v2/admin/localization')
      .then(({data: {data}}) => {
        this.configuration.language = this.languageList.find(
          item => item.id === data.language,
        );
        this.configuration.dateFormat = this.dateFormatList.find(
          item => item.id === 'Y-m-d',
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http.http
        .put('api/v2/admin/localization', {
          language: this.configuration.language?.id,
          dateFormat: this.configuration.dateFormat?.id,
        })
        .then(() => {
          reloadPage();
          this.$toast.updateSuccess();
        });
    },
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-header-container {
  padding: 0;
}
</style>
