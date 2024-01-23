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
        {{ $t('admin.add_provider') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="authProvider.name"
                :rules="rules.name"
                :label="$t('general.name')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="authProvider.url"
                :rules="rules.url"
                :label="$t('admin.url')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="authProvider.clientId"
                :rules="rules.clientId"
                :label="$t('admin.client_id')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="authProvider.clientSecret"
                :rules="rules.clientSecret"
                :label="$t('admin.client_secret')"
                type="password"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
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
} from '@ohrm/core/util/validation/rules';
import useServerValidation from '@/core/util/composable/useServerValidation';

const initialAuthProvider = {
  name: '',
  url: '',
  clientId: '',
  clientSecret: '',
};

export default {
  name: 'AddProvider',

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/auth/openid-providers',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const providerNameUniqueValidation = createUniqueValidator(
      'OpenIdProvider',
      'providerName',
      {
        matchByField: 'status',
        matchByValue: 1,
      },
    );
    return {
      http,
      providerNameUniqueValidation,
    };
  },
  data() {
    return {
      isLoading: false,
      authProvider: {...initialAuthProvider},
      rules: {
        name: [
          required,
          this.providerNameUniqueValidation,
          shouldNotExceedCharLength(40),
        ],
        clientId: [required, shouldNotExceedCharLength(255)],
        clientSecret: [required, shouldNotExceedCharLength(255)],
        url: [required, shouldNotExceedCharLength(2000)],
      },
    };
  },
  methods: {
    onCancel() {
      navigate('/admin/openIdProvider');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.authProvider,
          name: this.authProvider.name.trim(),
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
