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
      <oxd-text tag="h6" class="orangehrm-main-title">{{
        $t('admin.edit_user')
      }}</oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="user.role"
                type="select"
                :label="$t('general.user_role')"
                :rules="rules.role"
                :options="userRoles"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <employee-autocomplete
                v-model="user.employee"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                v-model="user.status"
                type="select"
                :label="$t('general.status')"
                :rules="rules.status"
                :options="userStatuses"
                required
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                v-model="user.username"
                :label="$t('general.username')"
                :rules="rules.username"
                required
                autocomplete="off"
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-field
                v-model="user.changePassword"
                type="checkbox"
                value="true"
                :true-value="true"
                :false-value="false"
                option-label="Yes"
                :label="$t('general.change_password_question')"
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
          <required-text />
          <oxd-button
            type="button"
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
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import PasswordInput from '@/core/components/inputs/PasswordInput';
import {
  required,
  shouldNotExceedCharLength,
  shouldNotLessThanCharLength,
} from '@ohrm/core/util/validation/rules';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';

const userModel = {
  id: '',
  username: '',
  role: null,
  employee: null,
  status: null,
  changePassword: false,
  password: '',
  passwordConfirm: '',
};

export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'password-input': PasswordInput,
  },
  props: {
    systemUserId: {
      type: String,
      required: true,
    },
  },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/admin/users');
    http.setIgnorePath('api/v2/admin/validation/user-name');
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
          shouldNotLessThanCharLength(5),
          shouldNotExceedCharLength(40),
          promiseDebounce(this.validateUserName, 500),
        ],
        role: [required],
        employee: [required],
        status: [required],
      },
      userRoles: [
        {id: 1, label: this.$t('general.admin')},
        {id: 2, label: this.$t('general.ess')},
      ],
      userStatuses: [
        {id: 1, label: this.$t('general.enabled')},
        {id: 2, label: this.$t('general.disabled')},
      ],
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.systemUserId)
      .then(response => {
        const {data} = response.data;
        this.user.id = data.id;
        this.user.username = data.userName;
        this.user.role = this.userRoles.find(
          item => item.id === data.userRole.id,
        );
        this.user.employee = {
          id: data.employee.empNumber,
          label: `${data.employee.firstName} ${data.employee.middleName} ${data.employee.lastName}`,
          isPastEmployee: data.employee.terminationId,
        };
        if (data.status) {
          this.user.status = {id: 1, label: this.$t('general.enabled')};
        } else {
          this.user.status = {id: 2, label: this.$t('general.disabled')};
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
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
          status: this.user.status && this.user.status.label === 'Enabled',
          userRoleId: this.user.role?.id,
          empNumber: this.user.employee?.id,
          changePassword: this.user.changePassword,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    validateUserName(user) {
      return new Promise(resolve => {
        if (user) {
          this.http
            .request({
              method: 'GET',
              url: `api/v2/admin/validation/user-name`,
              params: {
                userName: this.user.username.trim(),
                userId: this.systemUserId,
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve(this.$t('general.already_exists'));
            });
        } else {
          resolve(true);
        }
      });
    },
  },
};
</script>

<style src="./system-user.scss" lang="scss" scoped></style>
