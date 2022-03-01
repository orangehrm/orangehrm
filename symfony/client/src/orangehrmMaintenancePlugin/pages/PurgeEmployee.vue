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
      <oxd-text tag="h6" class="orangehrm-main-title">
        Purge Employee Records
      </oxd-text>

      <oxd-divider />

      <oxd-form @submit="displayPurgeableEmployee">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="purgeableEmployee.employee"
                :rules="rules.employee"
                :params="{
                  includeEmployees: purgeableEmployee.includeEmployeesParam,
                }"
                label="Past Employee"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button display-type="secondary" label="Search" type="submit" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />
    <div v-if="showPurgeableEmployee" class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        Selected Employee
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading">
        <div class="orangehrm-purge-employee">
          <div class="orangehrm-purge-employee-imagesection">
            <div class="orangehrm-purge-employee-image-wrapper">
              <div class="orangehrm-purge-employee-image">
                <img
                  alt="profile picture"
                  class="employee-image"
                  :src="imgSrc"
                />
              </div>
            </div>
          </div>
          <div class="orangehrm-edit-employee-content">
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <full-name-input
                    v-model:firstName="selectedEmployee.firstName"
                    v-model:middleName="selectedEmployee.middleName"
                    v-model:lastName="selectedEmployee.lastName"
                    :rules="rules"
                    disabled
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="selectedEmployee.employeeId"
                    label="Employee Id"
                    :rules="rules.employeeId"
                    disabled
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
          </div>
        </div>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button display-type="secondary" label="Purge" type="submit" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import RequiredText from '@/core/components/labels/RequiredText';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';

const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

const selectedEmployeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  employeeId: '',
  empNumber: '',
};

const purgeableEmployeeModel = {
  employee: null,
  includeEmployeesParam: 'onlyPast',
};

export default {
  name: 'PurgeEmployee',
  components: {
    'full-name-input': FullNameInput,
    'required-text': RequiredText,
    'employee-autocomplete': EmployeeAutocomplete,
  },

  data() {
    return {
      isLoading: false,
      showPurgeableEmployee: false,
      purgeableEmployee: {...purgeableEmployeeModel},
      selectedEmployee: {...selectedEmployeeModel},
      imgSrc: defaultPic,
      rules: {
        employee: [required],
        firstName: [shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [shouldNotExceedCharLength(30)],
        employeeId: [shouldNotExceedCharLength(10)],
      },
    };
  },

  methods: {
    displayPurgeableEmployee() {
      this.selectedEmployee = {...selectedEmployeeModel};
      this.imgSrc = defaultPic;
      this.isLoading = true;
      if (this.purgeableEmployee.employee) {
        this.selectedEmployee = {...this.purgeableEmployee.employee._employee};
        this.imgSrc = `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${this.purgeableEmployee.employee._employee.empNumber}`;
        this.showPurgeableEmployee = true;
      } else {
        this.showPurgeableEmployee = false;
      }
      this.isLoading = false;
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';
@import '@ohrm/oxd/styles/_colors.scss';

.orangehrm-purge-employee {
  display: flex;
  @include oxd-respond-to('xs') {
    flex-direction: column;
  }
  @include oxd-respond-to('md') {
    flex-direction: row;
  }

  &-content {
    flex: 1;
  }

  &-image-wrapper {
    padding-bottom: 1.2rem;
    @include oxd-respond-to('md') {
      padding-top: 1.2rem;
      padding-left: 7rem;
      padding-right: 7rem;
    }
  }

  &-image {
    width: 120px;
    height: 120px;
    border-radius: 100%;
    display: flex;
    cursor: pointer;
    overflow: hidden;
    justify-content: center;
    box-sizing: border-box;
    border: 0.5rem solid $oxd-background-pastel-white-color;
    box-shadow: 1px 1px 18px 11px hsl(238deg 13% 76% / 24%);
  }

  &-imagesection {
    display: flex;
    align-items: center;
    @include oxd-respond-to('xs') {
      flex-direction: row-reverse;
      justify-content: center;
    }
    @include oxd-respond-to('md') {
      flex-direction: column;
      justify-content: center;
    }
  }
}
</style>
