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
      <oxd-text class="orangehrm-main-title">
        {{ $t('pim.optional_fields') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title" tag="h6">
            {{ $t('pim.show_deprecated_fields') }}
          </oxd-text>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <div class="orangehrm-optional-field-row">
              <oxd-text tag="p" class="orangehrm-optional-field-label">
                {{
                  $t(
                    'pim.show_nick_name_smoker_and_military_service_in_personal_details',
                  )
                }}
              </oxd-text>
              <oxd-switch-input
                v-model="optionalField.pimShowDeprecatedFields"
              />
            </div>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title" tag="h6">
            {{ $t('pim.country_specific_information') }}
          </oxd-text>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <div class="orangehrm-optional-field-row">
              <oxd-text tag="p" class="orangehrm-optional-field-label">
                {{ $t('pim.show_ssn_field_in_personal_details') }}
              </oxd-text>
              <oxd-switch-input v-model="optionalField.showSSN" />
            </div>
            <div class="orangehrm-optional-field-row">
              <oxd-text tag="p" class="orangehrm-optional-field-label">
                {{ $t('pim.show_sin_field_in_personal_details') }}
              </oxd-text>
              <oxd-switch-input v-model="optionalField.showSIN" />
            </div>
            <div class="orangehrm-optional-field-row">
              <oxd-text tag="p" class="orangehrm-optional-field-label">
                {{ $t('pim.show_us_tax_exemptions_menu') }}
              </oxd-text>
              <oxd-switch-input v-model="optionalField.showTaxExemptions" />
            </div>
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
import {OxdSwitchInput} from '@ohrm/oxd';

const optionalFieldModel = {
  pimShowDeprecatedFields: false,
  showSSN: false,
  showSIN: false,
  showTaxExemptions: false,
};

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/optional-field',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      optionalField: {...optionalFieldModel},
    };
  },

  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        this.optionalField = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {...this.optionalField},
        })
        .then((response) => {
          const {data} = response.data;
          this.optionalField = {...data};
          this.$toast.saveSuccess();
          this.isLoading = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-optional-field-row {
  grid-column-start: 1;
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0.75rem;
}

.orangehrm-optional-field-label {
  @include oxd-input-control();
  padding: 0;
  flex-basis: 75%;
}
</style>
