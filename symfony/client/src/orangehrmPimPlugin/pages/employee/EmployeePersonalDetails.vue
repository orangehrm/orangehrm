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
  <edit-employee-layout :employee-id="empNumber" screen="personal">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6">Personal Details</oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
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
            :cols="3"
            v-if="showDeprecatedFields"
            class="orangehrm-full-width-grid"
          >
            <oxd-grid-item>
              <oxd-input-field label="Nickname" v-model="employee.nickname" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Employee Id"
                v-model="employee.employeeId"
                :rules="rules.employeeId"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field label="Other Id" v-model="employee.otherId" />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Driver's License Number"
                v-model="employee.drivingLicenseNo"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="License Expiry Date"
                v-model="employee.drivingLicenseExpiredDate"
              />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item v-if="showSSNField">
              <oxd-input-field label="SSN Number" v-model="employee.ssnNumb" />
            </oxd-grid-item>
            <oxd-grid-item v-if="showSINField">
              <oxd-input-field label="SIN Number" v-model="employee.sinNumb" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Nationality"
                v-model="employee.nationality"
                :clear="false"
                :options="nationalities"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Marital Status"
                v-model="employee.maritalStatus"
                :clear="false"
                :options="maritalStatuses"
              />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Date of Birth"
                v-model="employee.birthday"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-group
                label="Gender"
                :classes="{wrapper: '--gender-grouped-field'}"
              >
                <oxd-input-field
                  type="radio"
                  v-model="employee.gender"
                  optionLabel="Male"
                  value="1"
                />
                <oxd-input-field
                  type="radio"
                  v-model="employee.gender"
                  optionLabel="Female"
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
                label="Military Service"
                v-model="employee.militaryService"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="checkbox"
                label="Smoker"
                option-label="yes"
                v-model="employee.smoker"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import EditEmployeeLayout from '@orangehrm/orangehrmPimPlugin/components/EditEmployeeLayout';
import FullNameInput from '@orangehrm/orangehrmPimPlugin/components/FullNameInput';
import {
  required,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';

const employeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  employeeId: '',
  otherId: '',
  drivingLicenseNo: '',
  drivingLicenseExpiredDate: '',
  ssnNumb: '',
  sinNumb: '',
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
    showSSNField: {
      type: Boolean,
      default: false,
    },
    showSINField: {
      type: Boolean,
      default: false,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/pim/employees/${props.empNumber}/personal-details`,
    );

    return {
      http,
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
      },
      maritalStatuses: [
        {id: 'Single', label: 'Single'},
        {id: 'Married', label: 'Married'},
        {id: 'Other', label: 'Other'},
      ],
    };
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
            maritalStatus: this.employee.maritalStatus.map(item => item.id)[0],
            birthday: this.employee.birthday,
            nationalityId: this.employee.nationality.map(item => item.id)[0],
          },
        })
        .then(response => {
          this.updateModel(response);
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Updated',
          });
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateModel(response) {
      const {data} = response.data;
      this.employee = {...employeeModel, ...data};
      this.employee.maritalStatus = this.maritalStatuses.filter(
        item => item.id === data.maritalStatus,
      );
      this.employee.nationality = this.nationalities.filter(
        item => item.id === data.nationality?.id,
      );
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        this.updateModel(response);
        return this.http.request({
          method: 'GET',
          url: 'api/v2/pim/employees',
        });
      })
      .then(response => {
        const {data} = response.data;
        this.rules.employeeId.push(v => {
          const index = data.findIndex(item => item.employeeId == v);
          if (index > -1) {
            const {empNumber} = data[index];
            return empNumber != this.empNumber
              ? 'Employee Id already exists'
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
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
