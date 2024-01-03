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
        {{
          editMode
            ? $t('admin.edit_oauth_client')
            : $t('admin.add_oauth_client')
        }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="oAuthClient.name"
                :label="$t('general.name')"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="oAuthClient.redirectUri"
                :label="$t('admin.redirect_uri')"
                :rules="rules.redirectUri"
                required
              />
            </oxd-grid-item>
            <template v-if="editMode">
              <oxd-grid-item class="--offset-row-3">
                <oxd-input-field
                  v-model="oAuthClient.clientId"
                  :label="$t('admin.client_id')"
                  disabled
                />
              </oxd-grid-item>
              <oxd-grid-item v-if="showClientSecret" class="--offset-row-4">
                <oxd-alert
                  v-if="isSecretPlain"
                  type="warn"
                  :show="true"
                  :message="$t('admin.client_secret_warning_message')"
                ></oxd-alert>
                <oxd-input-field
                  v-model="oAuthClient.clientSecret"
                  :label="$t('admin.client_secret')"
                  disabled
                />
              </oxd-grid-item>
            </template>
            <oxd-grid-item class="--offset-row-5">
              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item class="orangehrm-field-row">
                  <oxd-text tag="p" class="orangehrm-field-label">
                    {{ $t('admin.enable_client') }}
                  </oxd-text>
                  <oxd-switch-input v-model="oAuthClient.enabled" />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-grid-item>
            <oxd-grid-item v-if="!editMode" class="--offset-row-6">
              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item class="orangehrm-field-row">
                  <oxd-text tag="p" class="orangehrm-field-label">
                    {{ $t('admin.confidential_client') }}
                  </oxd-text>
                  <oxd-switch-input v-model="oAuthClient.confidential" />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
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
import {OxdAlert, OxdSwitchInput} from '@ohrm/oxd';

const initialOAuthClient = {
  id: null,
  name: '',
  redirectUri: '',
  enabled: true,
  clientId: null,
  clientSecret: '********',
  confidential: false,
};

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'oxd-alert': OxdAlert,
  },
  props: {
    id: {
      type: Number,
      default: null,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/oauth-clients',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      isSecretPlain: false,
      oAuthClient: {...initialOAuthClient},
      rules: {
        name: [required, shouldNotExceedCharLength(80)],
        redirectUri: [required, shouldNotExceedCharLength(2000)],
      },
    };
  },

  computed: {
    editMode() {
      return this.oAuthClient.clientId !== null;
    },
    showClientSecret() {
      return this.oAuthClient.confidential === true;
    },
  },

  created() {
    this.isLoading = true;
    this.getClient()
      .then((response) => {
        const {data} = response.data;
        this.rules.name.push((v) => {
          const index = data.findIndex((item) => item.name === v);
          if (index > -1) {
            const {id} = data[index];
            return id !== this.id ? this.$t('general.already_exists') : true;
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
    getClient() {
      if (this.id !== null) {
        return this.http.get(this.id).then((response) => {
          const {data} = response.data;
          this.setDataFromResponse(data);

          // Fetch list data for unique test
          return this.http.getAll({limit: 0});
        });
      }
      return this.http.getAll({limit: 0});
    },
    onCancel() {
      navigate('/admin/registerOAuthClient');
    },
    onSave() {
      this.isLoading = true;
      (this.editMode ? this.update() : this.create()).finally(() => {
        this.isLoading = false;
      });
    },
    create() {
      return this.http
        .create({
          name: this.oAuthClient.name,
          redirectUri: this.oAuthClient.redirectUri,
          enabled: this.oAuthClient.enabled,
          confidential: this.oAuthClient.confidential,
        })
        .then((response) => {
          const {data, meta} = response.data;
          this.setDataFromResponse(data);
          this.oAuthClient.clientSecret = meta.clientSecret;
          this.isSecretPlain = true;

          return this.$toast.saveSuccess();
        });
    },
    update() {
      return this.http
        .update(this.oAuthClient.id, {
          name: this.oAuthClient.name,
          redirectUri: this.oAuthClient.redirectUri,
          enabled: this.oAuthClient.enabled,
          confidential: this.oAuthClient.confidential,
        })
        .then((response) => {
          const {data, meta} = response.data;
          this.setDataFromResponse(data);
          if (data.confidential === true && meta.clientSecret !== null) {
            this.oAuthClient.clientSecret = meta.clientSecret;
            this.isSecretPlain = true;
          }

          return this.$toast.updateSuccess();
        });
    },
    setDataFromResponse(data) {
      this.oAuthClient.id = data.id;
      this.oAuthClient.name = data.name;
      this.oAuthClient.redirectUri = data.redirectUri;
      this.oAuthClient.enabled = data.enabled;
      this.oAuthClient.clientId = data.clientId;
      this.oAuthClient.confidential = data.confidential;
    },
  },
};
</script>

<style src="./oauth-client.scss" lang="scss" scoped></style>
