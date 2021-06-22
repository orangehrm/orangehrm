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
      <oxd-text tag="h6" class="orangehrm-main-title">Edit User</oxd-text>
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

        <password-input
          v-if="user.changePassword"
          v-model:password="user.password"
          v-model:passwordConfirm="user.passwordConfirm"
        />

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
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import EmployeeDropdown from '@/core/components/inputs/EmployeeDropdown';
import PasswordInput from '@/core/components/inputs/PasswordInput';
import {required} from '@orangehrm/core/util/validation/rules';

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
    'employee-dropdown': EmployeeDropdown,
    'password-input': PasswordInput,
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
          required,
          v =>
            (v && v.trim().length >= 5) || 'Should have at least 5 characters',
          v =>
            (v && v.trim().length <= 40) || 'Should not exceed 40 characters',
        ],
        role: [v => (!!v && v.length != 0) || 'Required'],
        employee: [v => (!!v && v.length != 0) || 'Required'],
        status: [v => (!!v && v.length != 0) || 'Required'],
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
      navigate('/admin/viewSystemUsers');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.systemUserId, {
          username: this.user.username.trim(),
          password: this.user.password,
          status:
            this.user.status[0] && this.user.status[0].label === 'Enabled',
          userRoleId: this.user.role[0].id,
          empNumber: this.user.employee[0].id,
          changePassword: this.user.changePassword,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
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
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style src="./system-user.scss" lang="scss" scoped></style>
