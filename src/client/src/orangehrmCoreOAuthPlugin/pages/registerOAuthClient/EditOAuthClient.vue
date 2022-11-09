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
        Edit OAuth Client
      </oxd-text>

      <oxd-divider />

      <oxd-form novalidate="true" :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="oAuthClient.clientId"
                label="ID"
                :rules="rules.clientId"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="oAuthClient.clientSecret"
                label="Secret"
                :rules="rules.clientSecret"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="oAuthClient.redirectUri"
                label="Redirect URI"
                :rules="rules.redirectUri"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-text tag="span" class="orangehrm-link">
              API Documentation:
              <a
                class="orangehrm-link-url"
                href="https://orangehrm.github.io/orangehrm-api-doc"
              >
                https://orangehrm.github.io/orangehrm-api-doc
              </a>
            </oxd-text>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-text tag="span" class="orangehrm-link">
              PHP Sample App:
              <a
                class="orangehrm-link-url"
                href="https://github.com/orangehrm/api-sample-app-php"
              >
                https://github.com/orangehrm/api-sample-app-php
              </a>
            </oxd-text>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button display-type="ghost" label="Cancel" @click="onCancel" />
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

const initialOAuthClient = {
  clientId: '',
  clientSecret: '',
  redirectUri: '',
};

export default {
  props: {
    oauthClientId: {
      type: String,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/oauth-clients',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      oAuthClient: {...initialOAuthClient},
      rules: {
        clientId: [required, shouldNotExceedCharLength(80)],
        clientSecret: [required, shouldNotExceedCharLength(80)],
        redirectUri: [shouldNotExceedCharLength(2000)],
      },
    };
  },

  created() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url: `/api/v2/admin/oauth-client`,
        params: {
          clientId: this.oauthClientId,
        },
      })
      .then(response => {
        const {data} = response.data;
        this.oAuthClient.clientId = data.clientId;
        this.oAuthClient.clientSecret = data.clientSecret;
        this.oAuthClient.redirectUri = data.redirectUri;

        // Fetch list data for unique test
        return this.http.getAll({limit: 0});
      })
      .then(response => {
        const {data} = response.data;
        this.rules.clientId.push(v => {
          const index = data.findIndex(item => item.clientId === v);
          if (index > -1) {
            const {clientId} = data[index];
            return clientId !== this.oauthClientId ? 'Already exists' : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/admin/registerOAuthClient');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: `/api/v2/admin/oauth-client`,
          params: {
            clientId: this.oauthClientId,
          },
          data: {
            clientId: this.oAuthClient.clientId,
            clientSecret: this.oAuthClient.clientSecret,
            redirectUri: this.oAuthClient.redirectUri,
          },
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },
};
</script>

<style src="./oauth-client.scss" lang="scss" scoped></style>
