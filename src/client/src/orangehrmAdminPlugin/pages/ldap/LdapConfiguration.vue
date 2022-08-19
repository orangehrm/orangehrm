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
          {{ $t('general.ldap_configuration') }}
        </oxd-text>
        <oxd-switch-input
          v-model="configuration.enable"
          label-position="left"
          :option-label="$t('general.enable')"
        />
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-text tag="p" class="orangehrm-subtitle">
          {{ $t('admin.server_settings') }}
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.hostname"
                :label="$t('admin.host')"
                :rules="rules.hostname"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.ldap_host_input_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-column-half">
              <oxd-input-field
                v-model="configuration.port"
                :label="$t('admin.port')"
                :rules="rules.port"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.port_input_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.encryption"
                type="select"
                :options="encryptionOptions"
                :label="$t('admin.encryption')"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="configuration.ldapImplementation"
                type="select"
                :show-empty-selector="false"
                :options="ldapImplementationOptions"
                :label="$t('admin.ldap_implementation')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          {{ $t('admin.bind_settings') }}
        </oxd-text>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-ldap-switch">
              <oxd-text tag="p" class="orangehrm-ldap-switch-text">
                {{ $t('admin.bind_anonymously') }}
              </oxd-text>
              <oxd-switch-input v-model="configuration.bindAnonymously" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row v-if="!configuration.bindAnonymously">
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.distinguishedName"
                :label="$t('admin.distinguished_name')"
                :rules="rules.distinguishedName"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.distinguishedPassword"
                type="password"
                :label="$t('general.password')"
                :rules="rules.distinguishedPassword"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          {{ $t('admin.user_lookup_settings') }}
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.baseDistinguishedName"
                :label="$t('admin.base_distinguished_name')"
                :rules="rules.baseDistinguishedName"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.searchScope"
                type="select"
                :show-empty-selector="false"
                :options="searchScopeOptions"
                :label="$t('admin.search_scope')"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.search_scope_input_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="configuration.userNameAttribute"
                :label="$t('admin.user_name_attribute')"
                :rules="rules.userNameAttribute"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.user_name_input_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-4">
              <oxd-input-field
                v-model="configuration.groupObjectClass"
                :label="$t('admin.group_object_class')"
                :rules="rules.groupObjectClass"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-5">
              <oxd-input-field
                v-model="configuration.groupObjectFilter"
                :label="$t('admin.group_object_filter')"
                :rules="rules.groupObjectFilter"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-6">
              <oxd-input-field
                v-model="configuration.groupNameAttribute"
                :label="$t('admin.group_name_attribute')"
                :rules="rules.groupNameAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-7">
              <oxd-input-field
                v-model="configuration.groupMembersAttribute"
                :label="$t('admin.group_members_attribute')"
                :rules="rules.groupMembersAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-8">
              <oxd-input-field
                v-model="configuration.groupMembershipAttribute"
                :label="$t('admin.user_membership_attribute')"
                :rules="rules.groupMembershipAttribute"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          {{ $t('admin.data_mapping') }}
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.dataMapping.firstname"
                :label="$t('admin.firstname_field')"
                :rules="rules.firstnameAttribute"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.dataMapping.middlename"
                :label="$t('admin.middlename_field')"
                :rules="rules.middlenameAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-3">
              <oxd-input-field
                v-model="configuration.dataMapping.lastname"
                :label="$t('admin.lastname_field')"
                :rules="rules.lastnameAttribute"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-4">
              <oxd-input-field
                v-model="configuration.dataMapping.userStatus"
                :label="$t('admin.user_status_field')"
                :rules="rules.userStatusAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-5">
              <oxd-input-field
                v-model="configuration.dataMapping.workEmail"
                :label="$t('admin.work_email_field')"
                :rules="rules.workEmailAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-6">
              <oxd-input-field
                v-model="configuration.dataMapping.employeeId"
                :label="$t('admin.employee_id_field')"
                :rules="rules.employeeIdAttribute"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          {{ $t('admin.additional_settings') }}
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.syncInterval"
                :label="$t('admin.sync_interval')"
                :rules="rules.syncInterval"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="button"
            display-type="ghost"
            :label="$t('admin.test_connection')"
            @click="onClickTest"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {
  required,
  digitsOnly,
  validPortRange,
  validHostnameFormat,
  shouldNotExceedCharLength,
  numberShouldBeBetweenMinAndMaxValue,
} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';

const configurationModel = {
  enable: false,
  hostname: 'localhost',
  port: 389,
  encryption: null,
  ldapImplementation: null,
  bindAnonymously: true,
  distinguishedName: 'cn=admin,dc=example,dc=org',
  distinguishedPassword: 'admin',
  baseDistinguishedName: null,
  searchScope: null,
  userNameAttribute: 'cn',
  syncInterval: 60,
  groupObjectClass: 'group',
  groupObjectFilter: '(&(objectClass=group)(cn=*))',
  groupNameAttribute: 'cn',
  groupMembersAttribute: 'member',
  groupMembershipAttribute: 'memberOf',
};

const dataMappingModel = {
  firstname: 'givenName',
  lastname: 'sn',
  middlename: null,
  userStatus: null,
  workEmail: null,
  employeeId: null,
};

export default {
  components: {
    'oxd-switch-input': SwitchInput,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/ldap-config',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      configuration: {
        ...configurationModel,
        dataMapping: {...dataMappingModel},
      },
      encryptionOptions: [
        {
          id: 'tls',
          label: this.$t('admin.tls'),
        },
        {
          id: 'ssl',
          label: this.$t('admin.ssl'),
        },
      ],
      searchScopeOptions: [
        {
          id: 'sub',
          label: this.$t('admin.subtree'),
        },
        {
          id: 'oneLevel',
          label: this.$t('admin.one_level'),
        },
      ],
      ldapImplementationOptions: [
        {
          id: 'openldap',
          label: this.$t('admin.open_ldap_v3'),
        },
        {
          id: 'activeDirectory',
          label: this.$t('admin.ms_active_directory'),
        },
      ],
      rules: {
        hostname: [
          required,
          validHostnameFormat,
          shouldNotExceedCharLength(255),
        ],
        port: [required, validPortRange(5, 0, 65535)],
        distinguishedName: [required, shouldNotExceedCharLength(100)],
        distinguishedPassword: [required, shouldNotExceedCharLength(100)],
        baseDistinguishedName: [required, shouldNotExceedCharLength(100)],
        userNameAttribute: [required, shouldNotExceedCharLength(100)],
        firstnameAttribute: [required, shouldNotExceedCharLength(100)],
        lastnameAttribute: [required, shouldNotExceedCharLength(100)],
        syncInterval: [
          required,
          digitsOnly,
          numberShouldBeBetweenMinAndMaxValue(60, 1440),
        ],
        middlenameAttribute: [shouldNotExceedCharLength(100)],
        userStatusAttribute: [shouldNotExceedCharLength(100)],
        workEmailAttribute: [shouldNotExceedCharLength(100)],
        employeeIdAttribute: [shouldNotExceedCharLength(100)],
        groupObjectClass: [shouldNotExceedCharLength(100)],
        groupObjectFilter: [shouldNotExceedCharLength(100)],
        groupNameAttribute: [shouldNotExceedCharLength(100)],
        groupMembersAttribute: [shouldNotExceedCharLength(100)],
        groupMembershipAttribute: [shouldNotExceedCharLength(100)],
      },
    };
  },
  beforeMount() {
    // TODO: uncomment after API added
    // this.isLoading = true;
    // this.http
    //   .getAll()
    //   .then(response => {
    //     const {data} = response.data;
    //     this.configuration = {
    //       ...data,
    //       encryption: this.encryptionOptions.find(
    //         option => option.id === data.encryption,
    //       ),
    //       searchScope:
    //         this.searchScopeOptions.find(
    //           option => option.id === data.searchScope,
    //         ) || this.searchScopeOptions[0],
    //       ldapImplementation:
    //         this.ldapImplementationOptions.find(
    //           option => option.id === data.ldapImplementation,
    //         ) || this.ldapImplementationOptions[0],
    //     };
    //   })
    //   .finally(() => {
    //     this.isLoading = false;
    //   });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.configuration,
          encryption: this.configuration.encryption?.id,
          searchScope: this.configuration.searchScope?.id,
          ldapImplementation: this.configuration.ldapImplementation?.id,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        });
    },
    onClickTest() {
      // TODO
      // this.isLoading = true;
      // this.http
      //   .request({
      //     method: 'POST',
      //     url: 'api/v2/admin/ldap/connect',
      //     data: {
      //       ...this.configuration,
      //       ldapImplementation: this.configuration.ldapImplementation?.id,
      //       encryption: this.configuration.encryption?.id,
      //       searchScope: this.configuration.searchScope?.id,
      //     },
      //   })
      //   .then(response => {
      //     const {data, error} = response.data;
      //     if (data) {
      //       this.$toast.success({
      //         title: this.$t('general.success'),
      //         message: 'LDAP server connected!',
      //       });
      //     } else {
      //       this.$toast.warn({
      //         title: this.$t('general.warning'),
      //         message: error.message,
      //       });
      //     }
      //   })
      //   .finally(() => (this.isLoading = false));
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
.orangehrm-ldap-switch {
  display: flex;
  align-items: center;
  white-space: nowrap;
  justify-content: space-between;
  margin-bottom: 1rem;
  &-text {
    font-size: $oxd-input-control-font-size;
  }
}
</style>
