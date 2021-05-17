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
      <oxd-text tag="h6">Add Employee</oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <div class="orangehrm-employee-container">
          <div class="orangehrm-employee-image">
            <profile-image-input
              v-model="employee.profileImage"
              :rules="rules.profileImage"
              :imgSrc="profilePicUrl"
            />
          </div>
          <div class="orangehrm-employee-form">
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <full-name-input
                    v-model:firstName="employee.firstName"
                    v-model:middleName="employee.middleName"
                    v-model:lastName="employee.lastName"
                    :rules="rules"
                  />
                </oxd-grid-item>
              </oxd-grid>

              <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    label="Employee Id"
                    v-model="employee.employeeId"
                    :rules="rules.employeeId"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-divider />
            <oxd-form-row class="user-form-header">
              <oxd-text class="user-form-header-text" tag="p"
                >Create Login Details</oxd-text
              >
              <oxd-switch-input v-model="createLogin" />
            </oxd-form-row>

            <template v-if="createLogin">
              <oxd-form-row>
                <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                  <oxd-grid-item>
                    <oxd-input-field
                      label="Username"
                      v-model="user.username"
                      :rules="rules.username"
                      required
                    />
                  </oxd-grid-item>

                  <oxd-grid-item>
                    <oxd-input-group
                      label="Status"
                      :classes="{wrapper: '--status-grouped-field'}"
                    >
                      <oxd-input-field
                        type="radio"
                        v-model="user.status"
                        optionLabel="Enabled"
                        value="1"
                      />
                      <oxd-input-field
                        type="radio"
                        v-model="user.status"
                        optionLabel="Disabled"
                        value="2"
                      />
                    </oxd-input-group>
                  </oxd-grid-item>
                </oxd-grid>
              </oxd-form-row>

              <password-input
                v-model:password="user.password"
                v-model:passwordConfirm="user.passwordConfirm"
              />
            </template>
          </div>
        </div>

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
import SwitchInput from '@orangehrm/oxd/core/components/Input/SwitchInput';
import ProfileImageInput from '@/orangehrmPimPlugin/components/ProfileImageInput';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import PasswordInput from '@/core/components/inputs/PasswordInput';

const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

const employeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  profileImage: null,
  employeeId: '',
};

const userModel = {
  username: '',
  role: 2,
  employee: null,
  status: '1',
  password: '',
  passwordConfirm: '',
};

export default {
  components: {
    'oxd-switch-input': SwitchInput,
    'profile-image-input': ProfileImageInput,
    'full-name-input': FullNameInput,
    'password-input': PasswordInput,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      createLogin: false,
      employee: {...employeeModel},
      user: {...userModel},
      rules: {
        firstName: [v => (!!v && v.trim() !== '') || 'Required'],
        lastName: [v => (!!v && v.trim() !== '') || 'Required'],
        employeeId: [],
        profileImage: [
          v =>
            v === null ||
            (v && v.size && v.size <= 1024 * 1024) ||
            'Attachment size exceeded',
        ],
        username: [
          v => (!!v && v.trim() !== '') || 'Required',
          v => (v && v.length >= 5) || 'Should have at least 5 characters',
          v => (v && v.length <= 40) || 'Should not exceed 40 characters',
        ],
        status: [v => (!!v && v.length != 0) || 'Required'],
      },
    };
  },

  methods: {
    onCancel() {
      navigate('/pim/viewEmployeeList');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          firstName: this.employee.firstName,
          middleName: this.employee.middleName,
          lastName: this.employee.lastName,
          employeeId: this.employee.employeeId,
        })
        .then(response => {
          const {data} = response;
          if (this.createLogin && data?.data) {
            return this.http.http.post('api/v2/admin/users', {
              username: this.user.username,
              password: this.user.password,
              status: this.user.status == '1',
              userRoleId: this.user.role,
              empNumber: data.data.empNumber,
            });
          } else {
            return;
          }
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Saved!',
          });
        })
        .then(() => {
          // go back
          this.isLoading = false;
          this.employee = {...employeeModel};
          this.user = {...userModel};
          this.onCancel();
        });
    },
  },

  computed: {
    profilePicUrl() {
      if (this.employee.profileImage) {
        const file = this.employee.profileImage.base64;
        return `data:image/jpeg;base64,${file}`;
      } else {
        return defaultPic;
      }
    },
  },

  created() {
    this.isLoading = true;
    this.http.http
      .get('api/v2/admin/users')
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

<style src="./employee.scss" lang="scss" scoped></style>
