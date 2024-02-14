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
  <oxd-form
    class="orangehrm-installer-page"
    :loading="isLoading"
    @submit-valid="onSubmit"
  >
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Admin User Creation
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      Select the name, email address, username and password to create the admin
      user for your OrangeHRM Instance
    </oxd-text>
    <br />
    <oxd-grid :cols="4" class="orangehrm-full-width-grid">
      <oxd-grid-item>
        <oxd-input-field
          v-model="adminUser.firstName"
          :rules="rules.firstName"
          placeholder="First Name"
          label="Employee Name"
          required
        />
      </oxd-grid-item>
      <oxd-grid-item>
        <oxd-input-field
          v-model="adminUser.lastName"
          :rules="rules.lastName"
          placeholder="Last Name"
          label="&nbsp;"
        />
      </oxd-grid-item>
      <oxd-grid-item class="--offset-row-2 --span-column-2">
        <oxd-input-field
          v-model="adminUser.email"
          :rules="rules.email"
          label="Email"
          required
        />
      </oxd-grid-item>
      <oxd-grid-item class="--offset-row-2">
        <oxd-input-field
          v-model="adminUser.contact"
          :rules="rules.contact"
          label="Contact Number"
        />
      </oxd-grid-item>
      <oxd-grid-item class="--offset-row-3">
        <oxd-input-field
          v-model="adminUser.username"
          label="Admin Username"
          :rules="rules.username"
          required
        />
      </oxd-grid-item>
      <oxd-grid-item class="--offset-row-4">
        <oxd-input-field
          v-model="adminUser.password"
          type="password"
          label="Password"
          :rules="rules.password"
          required
        />
      </oxd-grid-item>
      <oxd-grid-item class="--offset-row-4">
        <oxd-input-field
          v-model="adminUser.confirmPassword"
          type="password"
          label="Confirm Password"
          :rules="rules.passwordConfirm"
          required
        />
      </oxd-grid-item>
    </oxd-grid>

    <br />
    <oxd-form-row class="orangehrm-register-consent">
      <oxd-input-field
        v-model="adminUser.registrationConsent"
        type="checkbox"
        option-label="Register your system with OrangeHRM. By registering, You will be eligible for free support via emails, receive security alerts and news letters from OrangeHRM."
      />
      <div class="orangehrm-register-notice">
        <oxd-icon class="orangehrm-register-notice-icon" name="info-circle" />
        <oxd-text class="orangehrm-register-notice-text" tag="p">
          Users who seek access to their data, or who seek to correct, amend, or
          delete the given information should direct their requests to
          data@orangehrm.com
        </oxd-text>
      </div>
    </oxd-form-row>

    <oxd-form-actions class="orangehrm-installer-page-action">
      <required-text />
      <oxd-button
        display-type="ghost"
        label="Back"
        type="button"
        @click="navigateUrl"
      />
      <oxd-button
        class="orangehrm-left-space"
        display-type="secondary"
        label="Next"
        type="submit"
      />
    </oxd-form-actions>
  </oxd-form>
</template>

<script>
import {
  required,
  validEmailFormat,
  validPhoneNumberFormat,
  shouldNotExceedCharLength,
  shouldNotLessThanCharLength,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import {checkPassword} from '@/core/util/helper/password';
import {APIService} from '@/core/util/services/api.service';
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'AdminUserCreation',
  components: {
    'oxd-icon': OxdIcon,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/installer/api/admin-user',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      adminUser: {
        firstName: null,
        lastName: null,
        email: null,
        contact: null,
        username: null,
        password: '',
        confirmPassword: '',
        registrationConsent: true,
      },
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        email: [required, shouldNotExceedCharLength(50), validEmailFormat],
        contact: [shouldNotExceedCharLength(25), validPhoneNumberFormat],
        username: [
          required,
          shouldNotExceedCharLength(40),
          shouldNotLessThanCharLength(5),
        ],
        password: [required, shouldNotExceedCharLength(64), checkPassword],
        passwordConfirm: [
          required,
          shouldNotExceedCharLength(64),
          (v) =>
            (!!v && v === this.adminUser.password) || 'Passwords do not match',
        ],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then((response) => {
      const {data} = response.data;
      this.adminUser = {...this.adminUser, ...data};
    });
    this.isLoading = false;
  },
  methods: {
    onSubmit() {
      this.isLoading = true;
      this.http
        .create({
          firstName: this.adminUser.firstName,
          lastName: this.adminUser.lastName,
          email: this.adminUser.email,
          contact: this.adminUser.contact,
          username: this.adminUser.username,
          password: this.adminUser.password,
          registrationConsent: this.adminUser.registrationConsent,
        })
        .then(() => {
          navigate('/installer/confirmation');
        });
    },
    navigateUrl() {
      navigate('/installer/instance-creation');
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.orangehrm-register-consent {
  max-width: 50%;
  ::v-deep(.oxd-checkbox-wrapper) {
    span {
      flex-shrink: 0;
    }
  }
}

.orangehrm-register-notice {
  display: flex;
  color: $oxd-interface-gray-color;
  &-icon {
    color: inherit;
    font-size: 16px;
    margin-right: 0.5em;
  }
  &-text {
    color: inherit;
  }
}
</style>
