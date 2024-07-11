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
  <edit-employee-layout :employee-id="empNumber" screen="personal">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('general.personal_details') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
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
          <oxd-grid
            v-if="showDeprecatedFields"
            :cols="3"
            class="orangehrm-full-width-grid"
          >
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.nickname"
                :label="$t('pim.nickname')"
                :rules="rules.nickname"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.employeeId"
                :label="$t('general.employee_id')"
                :rules="rules.employeeId"
                :disabled="!$can.update(`personal_sensitive_information`)"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.otherId"
                :label="$t('pim.other_id')"
                :rules="rules.otherId"
              />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.drivingLicenseNo"
                :label="$t('pim.driver_license_number')"
                :rules="rules.drivingLicenseNo"
                :disabled="!$can.update(`personal_sensitive_information`)"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="employee.drivingLicenseExpiredDate"
                :rules="rules.drivingLicenseExpiredDate"
                :label="$t('pim.license_expiry_date')"
              />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item v-if="showSsnField">
              <oxd-input-field
                v-model="employee.ssnNumber"
                :label="$t('pim.ssn_number')"
                :rules="rules.ssnNumber"
                :disabled="!$can.update(`personal_sensitive_information`)"
              />
            </oxd-grid-item>
            <oxd-grid-item v-if="showSinField">
              <oxd-input-field
                v-model="employee.sinNumber"
                :label="$t('pim.sin_number')"
                :rules="rules.sinNumber"
                :disabled="!$can.update(`personal_sensitive_information`)"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.nationality"
                type="select"
                :label="$t('general.nationality')"
                :clear="false"
                :options="nationalities"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.maritalStatus"
                type="select"
                :label="$t('pim.marital_status')"
                :clear="false"
                :options="maritalStatuses"
              />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                v-model="employee.birthday"
                :label="$t('pim.date_of_birth')"
                :rules="rules.birthday"
                :disabled="!$can.update(`personal_sensitive_information`)"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-group
                :label="$t('pim.gender')"
                :classes="{wrapper: '--gender-grouped-field'}"
              >
                <oxd-input-field
                  v-model="employee.gender"
                  type="radio"
                  :option-label="$t('general.male')"
                  value="1"
                />
                <oxd-input-field
                  v-model="employee.gender"
                  type="radio"
                  :option-label="$t('general.female')"
                  value="2"
                />
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider v-if="showDeprecatedFields" />
        <oxd-form-row v-if="showDeprecatedFields">
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.militaryService"
                :label="$t('pim.military_service')"
                :rules="rules.militaryService"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="employee.smoker"
                type="checkbox"
                :label="$t('pim.smoker')"
                :option-label="$t('general.yes')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@ohrm/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';

const employeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  employeeId: '',
  otherId: '',
  drivingLicenseNo: '',
  drivingLicenseExpiredDate: '',
  ssnNumber: '',
  sinNumber: '',
  nationality: [],
  maritalStatus: [],
  birthday: '',
  gender: '',
  nickname: '',
  smoker: '',
  militaryService: '',
};

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
    'full-name-input': FullNameInput,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    nationalities: {
      type: Array,
      default: () => [],
    },
    showDeprecatedFields: {
      type: Boolean,
      default: false,
    },
    showSsnField: {
      type: Boolean,
      default: false,
    },
    showSinField: {
      type: Boolean,
      default: false,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.empNumber}/personal-details`,
    );
    const {userDateFormat} = useDateFormat();

    return {
      http,
      userDateFormat,
    };
  },

  data() {
    return {
      isLoading: false,
      employee: {...employeeModel},
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        employeeId: [shouldNotExceedCharLength(10)],
        otherId: [shouldNotExceedCharLength(30)],
        drivingLicenseNo: [shouldNotExceedCharLength(30)],
        ssnNumber: [shouldNotExceedCharLength(30)],
        sinNumber: [shouldNotExceedCharLength(30)],
        nickname: [shouldNotExceedCharLength(30)],
        militaryService: [shouldNotExceedCharLength(30)],
        birthday: [validDateFormat(this.userDateFormat)],
        drivingLicenseExpiredDate: [validDateFormat(this.userDateFormat)],
      },
      maritalStatuses: [
        {id: 'Single', label: this.$t('pim.single')},
        {id: 'Married', label: this.$t('pim.married')},
        {id: 'Other', label: this.$t('pim.other')},
      ],
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        this.updateModel(response);
        return this.http.request({
          method: 'GET',
          url: '/api/v2/pim/employees',
        });
      })
      .then((response) => {
        const {data} = response.data;
        this.rules.employeeId.push((v) => {
          const index = data.findIndex(
            (item) =>
              item.employeeId?.trim() &&
              String(item.employeeId).toLowerCase() == String(v).toLowerCase(),
          );
          if (index > -1) {
            const {empNumber} = data[index];
            return empNumber != this.empNumber
              ? this.$t('pim.employee_id_exists')
              : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            lastName: this.employee.lastName,
            firstName: this.employee.firstName,
            middleName: this.employee.middleName,
            employeeId: this.employee.employeeId,
            otherId: this.employee.otherId,
            drivingLicenseNo: this.employee.drivingLicenseNo,
            drivingLicenseExpiredDate: this.employee.drivingLicenseExpiredDate,
            gender: this.employee.gender,
            maritalStatus: this.employee.maritalStatus?.id,
            birthday: this.employee.birthday,
            nationalityId: this.employee.nationality?.id,
            ssnNumber: this.showSsnField ? this.employee.ssnNumber : undefined,
            sinNumber: this.showSinField ? this.employee.sinNumber : undefined,
            nickname: this.showDeprecatedFields
              ? this.employee.nickname
              : undefined,
            smoker: this.showDeprecatedFields
              ? this.employee.smoker
              : undefined,
            militaryService: this.showDeprecatedFields
              ? this.employee.militaryService
              : undefined,
          },
        })
        .then((response) => {
          this.updateModel(response);
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateModel(response) {
      const {data} = response.data;
      this.employee = {...employeeModel, ...data};
      this.employee.maritalStatus = this.maritalStatuses.find(
        (item) => item.id === data.maritalStatus,
      );
      this.employee.nationality = this.nationalities.find(
        (item) => item.id === data.nationality?.id,
      );
    },
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
