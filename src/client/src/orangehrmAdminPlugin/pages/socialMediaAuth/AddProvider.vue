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
                :label="$t('admin.url')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="authProvider.clientId"
                :label="$t('admin.client_id')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="authProvider.clientSecret"
                :label="$t('admin.secret')"
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
  validURL,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import useServerValidation from '@/core/util/composable/useServerValidation';

const initialAuthProvider = {
  name: '',
  url: '',
  clientId: '',
  clientSecret: '',
  status: 1,
};

export default {
  name: 'AddProvider',

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/auth/providers',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const providerNameUniqueValidation = createUniqueValidator(
      'OpenIdProvider',
      'name',
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
        name: [required, shouldNotExceedCharLength(40)],
        url: {
          validURL,
        },
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
