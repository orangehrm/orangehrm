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
      <oxd-text tag="h6">Add User</oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="User Role"
                v-model="user.role"
                :rules="rules.role"
                :clear="false"
                :options="userRoles"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-dropdown
                v-model="user.employee"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Status"
                v-model="user.status"
                :rules="rules.status"
                :clear="false"
                :options="userStatuses"
                required
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                label="Username"
                v-model="user.username"
                :rules="rules.username"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row class="user-password-row">
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item class="user-password-cell">
              <oxd-chip
                v-if="user.password"
                :class="chipClasses"
                :label="passwordStrength"
              />
              <oxd-input-field
                label="Password"
                type="password"
                v-model="user.password"
                :rules="rules.password"
                required
              />
              <oxd-text class="user-password-hint" tag="p">
                For a strong password, please use a hard to guess combination of
                text with upper and lower case characters, symbols and numbers
              </oxd-text>
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                label="Confirm Password"
                type="password"
                v-model="user.passwordConfirm"
                :rules="rules.passwordConfirm"
                autocomplete="off"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <oxd-button displayType="ghost" label="Cancel" @click="onCancel" />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import Chip from '@orangehrm/oxd/core/components/Chip/Chip.vue';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {
  checkPassword,
  getPassLevel,
} from '@orangehrm/core/util/helper/password';
import {APIService} from '@/core/util/services/api.service';
import EmployeeDropdown from '@/orangehrmAdminPlugin/components/EmployeeDropdown';

const userModel = {
  username: '',
  role: [{id: 1, label: 'Admin'}],
  employee: [],
  status: [{id: 1, label: 'Enabled'}],
  password: '',
  passwordConfirm: '',
};

export default {
  components: {
    'oxd-chip': Chip,
    'employee-dropdown': EmployeeDropdown,
  },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/admin/users');
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      user: {...userModel},
      rules: {
        username: [
          v => (!!v && v.trim() !== '') || 'Required',
          v => (v && v.length <= 40) || 'Should not exceed 40 characters',
        ],
        role: [v => (!!v && v.length != 0) || 'Required'],
        employee: [v => (!!v && v.length != 0) || 'Required'],
        status: [v => (!!v && v.length != 0) || 'Required'],
        password: [
          v => (!!v && v.trim() !== '') || 'Required',
          v => (v && v.length <= 64) || 'Should not exceed 64 characters',
          v => checkPassword(v),
        ],
        passwordConfirm: [
          v => (!!v && v.trim() !== '') || 'Required',
          v => (!!v && v === this.user.password) || 'Passwords do not match',
        ],
      },
      userRoles: [
        {id: 1, label: 'Admin'},
        {id: 2, label: 'ESS'},
      ],
      userStatuses: [
        {id: 1, label: 'Enabled'},
        {id: 2, label: 'Disabled'},
      ],
    };
  },

  methods: {
    onCancel() {
      navigate('/admin/systemUser');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          username: this.user.username,
          password: this.user.password,
          status:
            this.user.status[0] && this.user.status[0].label === 'Enabled',
          userRoleId: this.user.role[0].id,
          empNumber: this.user.employee[0].id,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'System user added successfully!',
          });
        })
        .then(() => {
          // go back
          this.isLoading = false;
          this.user = {...userModel};
          this.onCancel();
        });
    },
  },

  computed: {
    passwordStrength() {
      let strength = 0;
      strength = getPassLevel(this.user.password).reduce(
        (acc, val) => acc + val,
        0,
      );
      if (this.user.password.trim().length < 8) {
        strength = 0;
      }
      switch (strength) {
        case 2:
          return 'Weak';
        case 3:
          return 'Better';
        case 4:
          return 'Strongest';
        default:
          return 'Very Weak';
      }
    },
    chipClasses() {
      return {
        'user-password-chip': true,
        '--green': this.passwordStrength === 'Strongest',
      };
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.rules.username.push(v => {
          const index = data.findIndex(item => item.userName == v);
          if (index > -1) {
            return 'Username already exists';
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style src="./system-user.scss" lang="scss" scoped></style>
