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
        {{ $t('general.add_employee') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <div class="orangehrm-employee-container">
          <div class="orangehrm-employee-image">
            <profile-image-input
              v-model="employee.empPicture"
              :rules="rules.empPicture"
              :img-src="profilePicUrl"
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
                    v-model="employee.employeeId"
                    :label="$t('general.employee_id')"
                    :rules="rules.employeeId"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-divider />
            <oxd-form-row class="user-form-header">
              <oxd-text class="user-form-header-text" tag="p">
                {{ $t('pim.create_login_details') }}
              </oxd-text>
              <oxd-switch-input v-model="createLogin" />
            </oxd-form-row>

            <template v-if="createLogin">
              <oxd-form-row>
                <oxd-grid :cols="2" class="orangehrm-full-width-grid">
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
                    <oxd-input-group
                      :label="$t('general.status')"
                      :classes="{wrapper: '--status-grouped-field'}"
                    >
                      <oxd-input-field
                        v-model="user.status"
                        type="radio"
                        :option-label="$t('general.enabled')"
                        value="1"
                      />
                      <oxd-input-field
                        v-model="user.status"
                        type="radio"
                        :option-label="$t('general.disabled')"
                        value="2"
                      />
                    </oxd-input-group>
                  </oxd-grid-item>
                </oxd-grid>
              </oxd-form-row>

              <password-input
                v-model:password="user.password"
                v-model:passwordConfirm="user.passwordConfirm"
                :is-password-required="isPasswordRequired"
              />
            </template>
          </div>
        </div>

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
import {ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import ProfileImageInput from '@/orangehrmPimPlugin/components/ProfileImageInput';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import PasswordInput from '@/core/components/inputs/PasswordInput';
import {
  maxFileSize,
  required,
  shouldNotExceedCharLength,
  shouldNotLessThanCharLength,
  validFileTypes,
} from '@ohrm/core/util/validation/rules';
import {OxdSwitchInput} from '@ohrm/oxd';
import useServerValidation from '@/core/util/composable/useServerValidation';

const defaultPic = `${window.appGlobal.publicPath}/images/default-photo.png`;

const employeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  empPicture: null,
  employeeId: '',
};

const userModel = {
  username: '',
  userRoleId: 2,
  empNumber: 0,
  status: '1',
  password: '',
  passwordConfirm: '',
};

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'profile-image-input': ProfileImageInput,
    'full-name-input': FullNameInput,
    'password-input': PasswordInput,
  },

  props: {
    empId: {
      type: String,
      required: true,
    },
    allowedImageTypes: {
      type: Array,
      required: true,
    },
    isPasswordRequired: {
      type: Boolean,
      default: true,
    },
  },

  setup(props) {
    const employee = ref({
      ...employeeModel,
      employeeId: props.empId ? props.empId : '',
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/employees',
    );

    const {createUniqueValidator} = useServerValidation(http);
    const employeeIdUniqueValidation = createUniqueValidator(
      'Employee',
      'employeeId',
      {translateKey: 'pim.employee_id_exists'},
    );
    const usernameUniqueValidation = createUniqueValidator('User', 'userName', {
      matchByField: 'deleted',
      matchByValue: 'false',
      translateKey: 'pim.username_already_exists',
    });

    return {
      http,
      employee,
      employeeIdUniqueValidation,
      usernameUniqueValidation,
    };
  },

  data() {
    return {
      isLoading: false,
      createLogin: false,
      user: {...userModel},
      empNumber: null,
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        employeeId: [
          this.employeeIdUniqueValidation,
          shouldNotExceedCharLength(10),
        ],
        empPicture: [
          maxFileSize(1024 * 1024),
          validFileTypes(this.allowedImageTypes),
        ],
        username: [
          required,
          this.usernameUniqueValidation,
          shouldNotLessThanCharLength(5),
          shouldNotExceedCharLength(40),
        ],
        status: [required],
      },
    };
  },

  computed: {
    profilePicUrl() {
      if (this.employee.empPicture) {
        const file = this.employee.empPicture.base64;
        const type = this.employee.empPicture.type;
        const isPicture = this.allowedImageTypes.findIndex(
          (item) => item === type,
        );
        return isPicture > -1 ? `data:${type};base64,${file}` : defaultPic;
      } else {
        return defaultPic;
      }
    },
  },

  created() {
    this.isLoading = true;
    this.http.getAll().finally(() => {
      this.isLoading = false;
    });
  },

  methods: {
    onCancel() {
      navigate('/pim/viewEmployeeList');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.employee,
        })
        .then((response) => {
          const {data} = response;
          if (data?.data) {
            this.empNumber = data.data.empNumber;
          }
          if (this.createLogin && data?.data) {
            return this.http.request({
              method: 'POST',
              url: '/api/v2/admin/users',
              data: {
                username: this.user.username,
                password: this.user.password,
                status: this.user.status == '1',
                userRoleId: this.user.userRoleId,
                empNumber: data.data.empNumber,
              },
            });
          } else {
            return;
          }
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.employee = {...employeeModel};
          this.user = {...userModel};
          if (this.empNumber) {
            navigate(`/pim/viewPersonalDetails/empNumber/${this.empNumber}`);
          } else {
            this.onCancel();
          }
        });
    },
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
