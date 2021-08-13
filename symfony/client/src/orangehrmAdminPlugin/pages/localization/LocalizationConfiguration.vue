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
        <oxd-text tag="h6" class="orangehrm-main-title">Localization</oxd-text>
        <oxd-switch-input
          v-model="editable"
          optionLabel="Edit"
          labelPosition="left"
        />
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Language"
                type="dropdown"
                v-model="configuration.language"
                :rules="rules.language"
                :options="languageList"
                :disabled="!editable"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item class="switch-form-field">
              <oxd-text class="switch-form-field-text" tag="p">
                Use Browser Language If Set
              </oxd-text>
              <oxd-switch-input
                :disabled="!editable"
                v-model="configuration.useBrowserLanguage"
              >
              </oxd-switch-input>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Date Format"
                type="dropdown"
                v-model="configuration.dateFormat"
                :rules="rules.dateFormat"
                :options="dateFormatList"
                :disabled="!editable"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button v-if="editable" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import SwitchInput from '@orangehrm/oxd/src/core/components/Input/SwitchInput';
import {required} from '@orangehrm/core/util/validation/rules';

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

  components: {
    'oxd-switch-input': SwitchInput,
  },

  data() {
    return {
      editable: false,
      isLoading: false,
      configuration: {
        language: '',
        dateFormat: '',
        useBrowserLanguage: false,
      },
      rules: {
        language: [required],
        dateFormat: [required],
      },
      errors: [],
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http.http
        .put('api/v2/admin/localization', {
          language: this.configuration.language[0]?.id,
          dateFormat: this.configuration.dateFormat[0]?.id,
          useBrowserLanguage: this.configuration.useBrowserLanguage,
          browserLanguage: navigator.language,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
          this.editable = false;
        });
    },
  },
  created() {
    this.isLoading = true;
    this.http.http
      .get('api/v2/admin/localization')
      .then(response => {
        const {data} = response.data;
        this.configuration.useBrowserLanguage = data.useBrowserLanguage;
        this.configuration.language = [
          this.languageList.find(l => {
            return l.id === data.defaultLanguage;
          }),
        ];
        this.configuration.dateFormat = [
          this.dateFormatList.find(f => {
            return f.id === data.defaultDateFormat;
          }),
        ];
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
<style lang="scss" scoped>
.switch-form-field {
  display: flex;
  padding: 1rem;
  &-text {
    font-size: 0.8rem;
    margin-right: 1rem;
  }
}
</style>
