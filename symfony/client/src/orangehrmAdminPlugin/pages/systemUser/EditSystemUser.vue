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
      <oxd-text tag="h6">Edit User</oxd-text>
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
              <oxd-input-field
                type="dropdown"
                label="Employee Name"
                v-model="user.employee"
                :rules="rules.employee"
                :create-options="loadEmployees"
                :lazyLoad="true"
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

            <oxd-grid-item>
              <oxd-input-field
                type="checkbox"
                value="true"
                :true-value="true"
                :false-value="false"
                v-model="user.changePassword"
                option-label="Yes"
                label="Change Password?"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row v-if="user.changePassword" class="user-password-row">
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
          <oxd-button
            class="orangehrm-left-space"
            displayType="secondary"
            label="Save"
            type="submit"
          />
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

const userModel = {
  id: '',
  username: '',
  role: [],
  employee: [],
  status: [],
  changePassword: false,
  password: '',
  passwordConfirm: '',
};

export default {
  props: {
    systemUserId: {
      type: String,
      required: true,
    },
  },

  components: {
    'oxd-chip': Chip,
  },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, 'api/v1/admin/users');
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
          v => (v && v.length < 100) || 'Should be less than 100 characters',
        ],
        role: [v => (!!v && v.length != 0) || 'Required'],
        employee: [v => (!!v && v.length != 0) || 'Required'],
        status: [v => (!!v && v.length != 0) || 'Required'],
        password: [
          v => (!!v && v.trim() !== '') || 'Required',
          v => (v && v.length < 100) || 'Should be less than 100 characters',
          v => checkPassword(v),
        ],
        passwordConfirm: [
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
        .update(this.systemUserId, {
          username: this.user.username,
          password: this.user.password,
          status:
            this.user.status[0] && this.user.status[0].label === 'Enabled',
          userRoleId: this.user.role[0].id,
          empNumber: 1,
          changePassword: this.user.changePassword,
        })
        .then(() => {
          this.isLoading = false;
          this.onCancel();
        })
        .catch(error => {
          console.log(error);
        });
    },
    async loadEmployees() {
      return new Promise(resolve => {
        setTimeout(() => {
          resolve([
            {
              id: 1,
              label: 'James Fox',
            },
            {
              id: 2,
              label: 'Darth Vader',
            },
            {
              id: 3,
              label: 'J Jhona Jamerson Jr.',
            },
          ]);
        }, 200);
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

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.systemUserId)
      .then(response => {
        const {data} = response.data;
        this.user.id = data.id;
        this.user.username = data.userName;
        this.user.role = this.userRoles.filter(
          item => item.id === data.userRole.id,
        );
        this.user.employee = [
          {
            id: data.employee.empNumber,
            label: `${data.employee.firstName} ${data.employee.lastName}`,
          },
        ];
        if (data.status) {
          this.user.status = [{id: 1, label: 'Enabled'}];
        } else {
          this.user.status = [{id: 2, label: 'Disabled'}];
        }
        return this.http.getAll();
      })
      .then(response => {
        const {data} = response.data;
        this.rules.username.push(v => {
          const index = data.findIndex(item => item.userName == v);
          if (index > -1) {
            const {id} = data[index];
            return id != this.user.id ? 'Username already exists' : true;
          } else {
            return true;
          }
        });
        this.isLoading = false;
      })
      .catch(error => {
        console.log(error);
      });
  },
};
</script>

<style src="./system-user.scss" lang="scss" scoped></style>
