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
      <oxd-text class="orangehrm-main-title">Optional Fields</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-text class="orangehrm-sub-title" tag="h6"
          >Show Deprecated Fields</oxd-text
        >
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-text tag="p">
                Show Nick Name, Smoker and Military Service in Personal Details
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-switch-input
                v-model="optionalField.pimShowDeprecatedFields"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-text class="orangehrm-sub-title" tag="h6"
          >Country Specific Information</oxd-text
        >
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-text tag="p">
                Show SSN field in Personal Details
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-switch-input v-model="optionalField.showSSN" />
            </oxd-grid-item>
            <oxd-grid-item> </oxd-grid-item>

            <oxd-grid-item>
              <oxd-text tag="p">
                Show SIN field in Personal Details
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-switch-input v-model="optionalField.showSIN" />
            </oxd-grid-item>
            <oxd-grid-item> </oxd-grid-item>

            <oxd-grid-item>
              <oxd-text tag="p">
                Show US Tax Exemptions menu
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-switch-input v-model="optionalField.showTaxExemptions" />
            </oxd-grid-item>
            <oxd-grid-item> </oxd-grid-item>
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
import {APIService} from '@orangehrm/core/util/services/api.service';
import SwitchInput from '@orangehrm/oxd/src/core/components/Input/SwitchInput';

const optionalFieldModel = {
  pimShowDeprecatedFields: false,
  showSSN: false,
  showSIN: false,
  showTaxExemptions: false,
};

export default {
  data() {
    return {
      isLoading: false,
      optionalField: {...optionalFieldModel},
    };
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/configures',
    );
    return {
      http,
    };
  },

  components: {
    'oxd-switch-input': SwitchInput,
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {...this.optionalField},
        })
        .then(response => {
          const {data} = response.data;
          this.optionalField = {...data};
          this.$toast.saveSuccess();
          this.isLoading = false;
        });
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.optionalField = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
