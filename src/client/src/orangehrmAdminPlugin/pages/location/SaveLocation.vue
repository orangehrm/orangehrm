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
        {{ $t('admin.add_location') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.name"
                :label="$t('general.name')"
                :rules="rules.name"
                required
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.city"
                :label="$t('general.city')"
                :rules="rules.city"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.province"
                :label="$t('general.state_province')"
                :rules="rules.province"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.zipCode"
                :label="$t('general.zip_postal_code')"
                :rules="rules.zipCode"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.countryCode"
                type="select"
                :label="$t('general.country')"
                :rules="rules.countryCode"
                :clear="false"
                :options="countries"
                required
                :disabled="!hasCreatePermissions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model.trim="location.phone"
                :label="$t('general.phone')"
                :rules="rules.phone"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.fax"
                :label="$t('general.fax')"
                :rules="rules.fax"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.address"
                type="textarea"
                :label="$t('admin.address')"
                :rules="rules.address"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="location.note"
                type="textarea"
                :label="$t('general.notes')"
                :rules="rules.note"
                :disabled="!hasCreatePermissions"
                :placeholder="$t('general.type_here_message')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button v-if="hasCreatePermissions" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
  validPhoneNumberFormat,
} from '@ohrm/core/util/validation/rules';
import useServerValidation from '@/core/util/composable/useServerValidation';

const initialLocation = {
  name: '',
  countryCode: null,
  province: '',
  city: '',
  address: '',
  zipCode: '',
  phone: '',
  fax: '',
  note: '',
};

export default {
  props: {
    countries: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/locations',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const locationUniqueValidation = createUniqueValidator('Location', 'name');

    return {
      http,
      locationUniqueValidation,
    };
  },

  data() {
    return {
      isLoading: false,
      location: {...initialLocation},
      rules: {
        name: [
          required,
          shouldNotExceedCharLength(100),
          this.locationUniqueValidation,
        ],
        countryCode: [required],
        province: [shouldNotExceedCharLength(50)],
        city: [shouldNotExceedCharLength(50)],
        address: [shouldNotExceedCharLength(250)],
        zipCode: [shouldNotExceedCharLength(30)],
        phone: [shouldNotExceedCharLength(30), validPhoneNumberFormat],
        fax: [shouldNotExceedCharLength(30), validPhoneNumberFormat],
        note: [shouldNotExceedCharLength(250)],
      },
    };
  },

  computed: {
    hasCreatePermissions() {
      return this.$can.create(`locations`);
    },
  },

  methods: {
    onCancel() {
      navigate('/admin/viewLocations');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.location,
          countryCode: this.location.countryCode.id,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },
};
</script>
