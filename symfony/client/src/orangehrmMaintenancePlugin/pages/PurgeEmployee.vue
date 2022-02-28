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

      <oxd-form @submit="displayEmployee">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.employee"
                :params="{includeEmployees: filters.includeEmployees?.param}"
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
        <div class="orangehrm-edit-employee">
          <div class="orangehrm-edit-employee-imagesection">
            <div class="orangehrm-edit-employee-image-wrapper">
              <div class="orangehrm-edit-employee-image">
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
                    v-model:firstName="purgeableEmployee.firstName"
                    v-model:middleName="purgeableEmployee.middleName"
                    v-model:lastName="purgeableEmployee.lastName"
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
                    v-model="purgeableEmployee.employeeId"
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
import {computed, ref} from 'vue';
import usePaginate from '@/core/util/composable/usePaginate';
import {APIService} from '@/core/util/services/api.service';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import RequiredText from '@/core/components/labels/RequiredText';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';

const defaultFilters = {
  employee: null,
  includeEmployees: {
    param: 'onlyPast',
  },
};

const defaultPic = `${window.appGlobal.baseUrl}/../dist/img/user-default-400.png`;

const purgeableEmployeeModel = {
  empNumber: '',
  firstName: '',
  middleName: '',
  lastName: '',
  employeeId: '',
};

export default {
  name: 'PurgeEmployee',
  components: {
    FullNameInput,
    RequiredText,
    'employee-autocomplete': EmployeeAutocomplete,
  },

  setup() {
    const filters = ref({...defaultFilters});

    const serializedFilters = computed(() => {
      return {
        empNumber: filters.value.employee?.id,
        includeEmployees: filters.value.includeEmployees?.param,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
    );
    const {response, execQuery} = usePaginate(http, {
      query: serializedFilters,
    });

    return {
      execQuery,
      items: response,
      filters,
    };
  },

  data() {
    return {
      isLoading: false,
      showPurgeableEmployee: false,
      purgeableEmployee: {...purgeableEmployeeModel},
      imgSrc: defaultPic,
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        employeeId: [shouldNotExceedCharLength(10)],
      },
    };
  },

  methods: {
    async displayEmployee() {
      this.purgeableEmployee = {...purgeableEmployeeModel};
      this.imgSrc = defaultPic;
      this.isLoading = true;
      if (this.filters.employee) {
        this.showPurgeableEmployee = true;
        await this.execQuery();
        this.purgeableEmployee = {
          ...purgeableEmployeeModel,
          ...this.items?.data[0],
        };
        this.imgSrc = `${window.appGlobal.baseUrl}/pim/viewPhoto/empNumber/${this.purgeableEmployee.empNumber}`;
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

.orangehrm-edit-employee {
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
