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

      <oxd-form ref="formRef" :loading="isLoading">
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
                v-model="configuration.bindUserDN"
                :label="$t('admin.distinguished_name')"
                :rules="rules.bindUserDN"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.bindUserPassword"
                type="password"
                :label="$t('general.password')"
                :placeholder="passwordPlaceHolder"
                :rules="rules.bindUserPassword"
                :required="!configuration.hasBindUserPassword"
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
                v-model="configuration.userSearchFilter"
                :label="$t('admin.user_search_filter')"
                :rules="rules.userSearchFilter"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.user_search_filter_input_hint') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-5">
              <oxd-input-field
                v-model="configuration.userUniqueIdAttribute"
                :label="$t('admin.user_unique_id_attribute')"
                :rules="rules.userUniqueIdAttribute"
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{ $t('admin.user_unique_attribute_input_hint') }}
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider class="orangehrm-form-divider" />

        <oxd-text tag="p" class="orangehrm-subtitle">
          {{ $t('admin.data_mapping') }}
        </oxd-text>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-ldap-grid">
            <oxd-grid-item class="orangehrm-ldap-grid-header">
              <oxd-text tag="p">
                {{ $t('admin.field_in_orangehrm') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-ldap-grid-header">
              <oxd-text tag="p">
                {{ $t('admin.field_in_ldap_directory') }}
              </oxd-text>
            </oxd-grid-item>
            <oxd-grid-item class="orangehrm-ldap-grid-header">
              <oxd-text tag="p">
                {{
                  $t('admin.use_this_field_as_the_employee_user_mapping_field')
                }}
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item class="orangehrm-ldap-grid-content">
              <oxd-text tag="p" class="oxd-input-field-required">
                {{ $t('general.first_name') }}
              </oxd-text>
              <oxd-icon class="orangehrm-ldap-grid-icon" name="arrow-left" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.dataMapping.firstName"
                :rules="rules.firstNameAttribute"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item></oxd-grid-item>

            <oxd-grid-item class="orangehrm-ldap-grid-content">
              <oxd-text tag="p">
                {{ $t('general.middle_name') }}
              </oxd-text>
              <oxd-icon class="orangehrm-ldap-grid-icon" name="arrow-left" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.dataMapping.middleName"
                :rules="rules.middleNameAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item></oxd-grid-item>

            <oxd-grid-item class="orangehrm-ldap-grid-content">
              <oxd-text tag="p" class="oxd-input-field-required">
                {{ $t('general.last_name') }}
              </oxd-text>
              <oxd-icon class="orangehrm-ldap-grid-icon" name="arrow-left" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.dataMapping.lastName"
                :rules="rules.lastNameAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item></oxd-grid-item>

            <oxd-grid-item class="orangehrm-ldap-grid-content">
              <oxd-text tag="p">
                {{ $t('general.user_status') }}
              </oxd-text>
              <oxd-icon class="orangehrm-ldap-grid-icon" name="arrow-left" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="configuration.dataMapping.userStatus"
                :rules="rules.userStatusAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item></oxd-grid-item>

            <oxd-grid-item class="orangehrm-ldap-grid-content">
              <oxd-text tag="p" :class="workEmailLabelClasses">
                {{ $t('general.work_email') }}
              </oxd-text>
              <oxd-icon class="orangehrm-ldap-grid-icon" name="arrow-left" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :key="configuration.employeeSelectorMapping"
                v-model="configuration.dataMapping.workEmail"
                :rules="rules.workEmailAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-switch-input
                v-model="configuration.employeeSelectorMapping"
                true-value="workEmail"
              />
            </oxd-grid-item>

            <oxd-grid-item class="orangehrm-ldap-grid-content">
              <oxd-text tag="p" :class="employeeIdLabelClasses">
                {{ $t('general.employee_id') }}
              </oxd-text>
              <oxd-icon class="orangehrm-ldap-grid-icon" name="arrow-left" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :key="configuration.employeeSelectorMapping"
                v-model="configuration.dataMapping.employeeId"
                :rules="rules.employeeIdAttribute"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-switch-input
                v-model="configuration.employeeSelectorMapping"
                true-value="employeeId"
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
            <oxd-grid-item class="orangehrm-ldap-switch --offset-row-1">
              <oxd-text tag="p" class="orangehrm-ldap-switch-text">
                {{ $t('admin.merge_ldap_users_with_existing_system_users') }}
              </oxd-text>
              <oxd-switch-input
                v-model="configuration.mergeLDAPUsersWithExistingSystemUsers"
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="configuration.syncInterval"
                :label="$t('admin.sync_interval')"
                :rules="rules.syncInterval"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-alert
          type="warn"
          :show="true"
          :message="$t('admin.ldap_configuration_warning_message')"
        ></oxd-alert>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            type="button"
            display-type="ghost"
            :label="$t('admin.test_connection')"
            @click="onClickTest"
          />
          <oxd-button
            type="button"
            class="orangehrm-left-space"
            display-type="secondary"
            :label="$t('general.save')"
            @click="onClickSave"
          />
        </oxd-form-actions>
      </oxd-form>
    </div>

    <ldap-test-connection-modal
      v-if="testModalState"
      :data="testModalState"
      @close="onCloseTestModal"
    ></ldap-test-connection-modal>

    <br />

    <ldap-sync-connection v-if="showSync"></ldap-sync-connection>
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
import useForm from '@/core/util/composable/useForm';
import {reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import LdapSyncConnection from '@/orangehrmAdminPlugin/components/LdapSyncConnection';
import LdapTestConnectionModal from '@/orangehrmAdminPlugin/components/LdapTestConnectionModal';
import {OxdAlert, OxdIcon, OxdSwitchInput} from '@ohrm/oxd';

const configurationModel = {
  enable: false,
  hostname: 'localhost',
  port: 389,
  encryption: null,
  ldapImplementation: null,
  bindAnonymously: true,
  bindUserDN: null,
  bindUserPassword: null,
  baseDistinguishedName: null,
  searchScope: null,
  userNameAttribute: 'cn',
  userSearchFilter: 'objectClass=person',
  userUniqueIdAttribute: null,
  mergeLDAPUsersWithExistingSystemUsers: false,
  syncInterval: 1,
  employeeSelectorMapping: '',
  hasBindUserPassword: false,
};

const dataMappingModel = {
  firstName: 'givenName',
  lastName: 'sn',
  middleName: null,
  userStatus: null,
  workEmail: null,
  employeeId: null,
};

export default {
  components: {
    'oxd-icon': OxdIcon,
    'oxd-alert': OxdAlert,
    'oxd-switch-input': OxdSwitchInput,
    'ldap-sync-connection': LdapSyncConnection,
    'ldap-test-connection-modal': LdapTestConnectionModal,
  },

  props: {
    showSync: {
      type: Boolean,
      default: false,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/ldap-config',
    );
    const {formRef, invalid, validate} = useForm();

    return {
      http,
      formRef,
      invalid,
      validate,
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
          id: 'one',
          label: this.$t('admin.one_level'),
        },
      ],
      ldapImplementationOptions: [
        {
          id: 'OpenLDAP',
          label: this.$t('admin.open_ldap_v3'),
        },
        {
          id: 'ActiveDirectory',
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
        bindUserDN: [required, shouldNotExceedCharLength(255)],
        bindUserPassword: [
          (v) => this.configuration.hasBindUserPassword || required(v),
          shouldNotExceedCharLength(255),
        ],
        baseDistinguishedName: [required, shouldNotExceedCharLength(255)],
        userNameAttribute: [required, shouldNotExceedCharLength(100)],
        userSearchFilter: [required, shouldNotExceedCharLength(100)],
        userUniqueIdAttribute: [shouldNotExceedCharLength(100)],
        firstNameAttribute: [required, shouldNotExceedCharLength(100)],
        lastNameAttribute: [required, shouldNotExceedCharLength(100)],
        syncInterval: [
          required,
          digitsOnly,
          numberShouldBeBetweenMinAndMaxValue(1, 23),
        ],
        middleNameAttribute: [shouldNotExceedCharLength(100)],
        userStatusAttribute: [shouldNotExceedCharLength(100)],
        workEmailAttribute: [
          (v) =>
            this.configuration.employeeSelectorMapping === 'workEmail'
              ? required(v)
              : true,
          shouldNotExceedCharLength(100),
        ],
        employeeIdAttribute: [
          (v) =>
            this.configuration.employeeSelectorMapping === 'employeeId'
              ? required(v)
              : true,
          shouldNotExceedCharLength(100),
        ],
      },
      testModalState: null,
    };
  },
  computed: {
    passwordPlaceHolder() {
      return this.configuration.hasBindUserPassword ? '********' : null;
    },
    workEmailLabelClasses() {
      return {
        'oxd-input-field-required':
          this.configuration.employeeSelectorMapping === 'workEmail',
      };
    },
    employeeIdLabelClasses() {
      return {
        'oxd-input-field-required':
          this.configuration.employeeSelectorMapping === 'employeeId',
      };
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        const {userLookupSettings} = data;
        const userLookupSetting = userLookupSettings[0];
        this.configuration.enable = data.enable;
        this.configuration.hostname = data.hostname;
        this.configuration.port = data.port;
        this.configuration.encryption = this.encryptionOptions.find(
          (option) => option.id === data.encryption,
        );
        this.configuration.ldapImplementation =
          this.ldapImplementationOptions.find(
            (option) => option.id === data.ldapImplementation,
          ) || this.ldapImplementationOptions[0];

        this.configuration.bindAnonymously = data.bindAnonymously;
        this.configuration.bindUserDN = data.bindUserDN;
        this.configuration.hasBindUserPassword = data.hasBindUserPassword;

        if (userLookupSetting) {
          this.configuration.baseDistinguishedName = userLookupSetting?.baseDN;
          this.configuration.userNameAttribute =
            userLookupSetting?.userNameAttribute;
          this.configuration.userSearchFilter =
            userLookupSetting?.userSearchFilter;
          this.configuration.userUniqueIdAttribute =
            userLookupSetting?.userUniqueIdAttribute;

          if (Array.isArray(userLookupSetting?.employeeSelectorMapping)) {
            if (userLookupSetting.employeeSelectorMapping.length === 0) {
              this.configuration.employeeSelectorMapping = '';
            } else {
              this.configuration.employeeSelectorMapping =
                userLookupSetting.employeeSelectorMapping[0]['field'];
            }
          }
        }
        this.configuration.searchScope =
          this.searchScopeOptions.find(
            (option) => option.id === userLookupSetting?.searchScope,
          ) || this.searchScopeOptions[0];

        this.configuration.dataMapping = data.dataMapping;
        this.configuration.mergeLDAPUsersWithExistingSystemUsers =
          data.mergeLDAPUsersWithExistingSystemUsers;
        this.configuration.syncInterval = data.syncInterval;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onClickTest() {
      this.validate().then(() => {
        if (this.invalid === true) return;
        this.isLoading = true;
        const data = this.getRequestBody();
        delete data.enable;
        delete data.syncInterval;
        this.http
          .request({
            method: 'POST',
            url: '/api/v2/admin/ldap-test-connection',
            data,
          })
          .then((response) => {
            const {data} = response.data;
            this.testModalState = data;
          })
          .finally(() => (this.isLoading = false));
      });
    },
    getRequestBody() {
      let employeeSelectorMapping;
      if (this.configuration.employeeSelectorMapping) {
        employeeSelectorMapping = [
          {
            field: this.configuration.employeeSelectorMapping,
            attributeName:
              this.configuration.dataMapping[
                this.configuration.employeeSelectorMapping
              ],
          },
        ];
      }

      return {
        enable: this.configuration.enable,
        hostname: this.configuration.hostname,
        port: parseInt(this.configuration.port),
        encryption: this.configuration.encryption?.id || 'none',
        ldapImplementation: this.configuration.ldapImplementation?.id,
        bindAnonymously: this.configuration.bindAnonymously,
        bindUserDN: this.configuration.bindUserDN,
        bindUserPassword: this.configuration.bindUserPassword,
        userLookupSettings: [
          {
            baseDN: this.configuration.baseDistinguishedName,
            searchScope: this.configuration.searchScope?.id,
            userNameAttribute: this.configuration.userNameAttribute,
            userSearchFilter: this.configuration.userSearchFilter,
            userUniqueIdAttribute: this.configuration.userUniqueIdAttribute,
            employeeSelectorMapping: employeeSelectorMapping || [],
          },
        ],
        dataMapping: this.configuration.dataMapping,
        mergeLDAPUsersWithExistingSystemUsers:
          this.configuration.mergeLDAPUsersWithExistingSystemUsers,
        syncInterval: parseInt(this.configuration.syncInterval),
      };
    },
    onClickSave() {
      this.validate().then(() => {
        if (this.invalid === true) return;
        this.isLoading = true;
        this.http
          .request({
            method: 'PUT',
            data: this.getRequestBody(),
          })
          .then(() => {
            return this.$toast.updateSuccess();
          })
          .finally(() => reloadPage());
      });
    },
    onCloseTestModal() {
      this.testModalState = null;
    },
  },
};
</script>

<style src="./ldap-configuration.scss" lang="scss" scoped></style>
