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
  <div class="orangehrm-card-container">
    <oxd-text tag="h6" class="orangehrm-main-title">
      Selected Employee
    </oxd-text>

    <oxd-divider />

    <oxd-form @submit="emitEmpNumber">
      <div class="orangehrm-selected-employee">
        <div class="orangehrm-selected-employee-imagesection">
          <div class="orangehrm-selected-employee-image-wrapper">
            <div class="orangehrm-selected-employee-image">
              <img alt="profile picture" class="employee-image" :src="imgSrc" />
            </div>
          </div>
        </div>
        <div class="orangehrm-selected-employee-content">
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
        <oxd-button
          display-type="secondary"
          :label="buttonLabel"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {shouldNotExceedCharLength} from '@/core/util/validation/rules';

export default {
  name: 'SelectedEmployee',

  components: {'full-name-input': FullNameInput},

  props: {
    employee: {
      type: Object,
      required: true,
    },
    imgSrc: {
      type: String,
      required: true,
    },
    buttonLabel: {
      type: String,
      required: true,
    },
  },

  emits: ['submit'],

  data() {
    return {
      selectedEmployee: this.employee,
      rules: {
        firstName: [shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [shouldNotExceedCharLength(30)],
        employeeId: [shouldNotExceedCharLength(10)],
      },
    };
  },

  methods: {
    emitEmpNumber() {
      this.$emit('submit', this.selectedEmployee.empNumber);
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';
@import '@ohrm/oxd/styles/_colors.scss';

.orangehrm-selected-employee {
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
