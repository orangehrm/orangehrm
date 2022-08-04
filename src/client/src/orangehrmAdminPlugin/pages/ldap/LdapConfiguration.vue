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
        <oxd-text tag="h6" class="orangehrm-main-title">
          LDAP Configuration
        </oxd-text>
        <oxd-switch-input
          v-model="configuration.enable"
          option-label="Enable"
          label-position="left"
        />
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-text tag="p" class="orangehrm-subtitle">
          Server Settings
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.hostname"
                label="Host URL"
                placeholder="IP or Hostname only"
              />
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-column-half">
              <oxd-input-field v-model="configuration.port" label="Port" />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.encryption"
                type="select"
                label="Encryption"
                :options="encryptionOptions"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="configuration.protocol"
                type="select"
                label="LDAP Implementation"
                :show-empty-selector="false"
                :options="ldapImplementationOptions"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          Bind Settings
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.distinguishedName"
                label="Distinguished Name"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.distinguishedPassword"
                type="password"
                label="Password"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          User Lookup Settings
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.baseDistinguishedName"
                label="Base Distinguished Name"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.searchScope"
                type="select"
                label="Search Scope"
                :show-empty-selector="false"
                :options="searchScopeOptions"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="configuration.userAttribute"
                type="select"
                label="User Attribute"
                :show-empty-selector="false"
                :options="userAttributeOptions"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          Data Mapping
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.adminRoleBaseDistinguishedNames"
                label="Admin Role Base Distinguished Names"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.essRoleBaseDistinguishedNames"
                label="ESS Role Base Distinguished Names"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="configuration.userStatusMappingField"
                label="User Status Field"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-4">
              <oxd-input-field
                v-model="configuration.firstNameMappingField"
                label="Firstname Field"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-5">
              <oxd-input-field
                v-model="configuration.middleNameMappingField"
                label="Middlename Field"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-6">
              <oxd-input-field
                v-model="configuration.lastNameMappingField"
                label="Lastname Field"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-7">
              <oxd-input-field
                v-model="configuration.workEmailMappingField"
                label="Work E-mail Field"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-8">
              <oxd-input-field
                v-model="configuration.employeeIdMappingField"
                label="Employee ID Field"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="button"
            display-type="ghost"
            label="Test Connection"
            @click="onClickTest"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';

export default {
  components: {
    'oxd-switch-input': SwitchInput,
  },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/admin/ldap');
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      configuration: {
        enable: false,
        hostname: 'localhost',
        port: 389,
        encryption: null,
        protocol: {
          id: 'openldap',
          label: 'Open LDAP v3',
        },
        distinguishedName: 'cn=admin,dc=example,dc=org',
        distinguishedPassword: 'admin',
        baseDistinguishedName: null,
        searchScope: {
          id: 'oneLevel',
          label: 'One level',
        },
        userAttribute: {
          id: 'cn',
          label: 'Common Name',
        },
        userStatusMappingField: null,
        firstNameMappingField: null,
        middleNameMappingField: null,
        lastNameMappingField: null,
        workEmailMappingField: null,
        employeeIdMappingField: null,
        essRoleBaseDistinguishedNames: null,
        adminRoleBaseDistinguishedNames: null,
      },
      encryptionOptions: [
        {
          id: 'tls',
          label: 'TLS',
        },
        {
          id: 'ssl',
          label: 'SSL',
        },
      ],
      ldapImplementationOptions: [
        {
          id: 'openldap',
          label: 'Open LDAP v3',
        },
        {
          id: 'activeDirectory',
          label: 'MS Active Directory',
        },
      ],
      searchScopeOptions: [
        {
          id: 'subTree',
          label: 'Subtree',
        },
        {
          id: 'oneLevel',
          label: 'One level',
        },
      ],
      userAttributeOptions: [
        {
          id: 'cn',
          label: 'Common Name',
        },
        {
          id: 'uid',
          label: 'User ID',
        },
      ],
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.configuration,
          protocol: this.configuration.protocol?.id,
          encryption: this.configuration.encryption?.id,
          searchScope: this.configuration.searchScope?.id,
          userAttribute: this.configuration.userAttribute?.id,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        });
    },
    onClickTest() {
      this.isLoading = true;
      this.http
        .request({
          method: 'POST',
          url: 'api/v2/admin/ldap/connect',
          data: {
            ...this.configuration,
            protocol: this.configuration.protocol?.id,
            encryption: this.configuration.encryption?.id,
            searchScope: this.configuration.searchScope?.id,
            userAttribute: this.configuration.userAttribute?.id,
          },
        })
        .then(response => {
          const {data, error} = response.data;
          if (data) {
            this.$toast.success({
              title: this.$t('general.success'),
              message: 'LDAP server connected!',
            });
          } else {
            this.$toast.warn({
              title: this.$t('general.warning'),
              message: error.message,
            });
          }
        })
        .finally(() => (this.isLoading = false));
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-header-container {
  padding: 0;
}
.orangehrm-column-half {
  width: 50%;
}
.orangehrm-form-divider {
  margin: 1rem 0;
}
.orangehrm-subtitle {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 1rem;
}
</style>
