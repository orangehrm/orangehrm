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
  <oxd-sheet
    :class="{'orangehrm-directory-card-height': hasDefaultSlot}"
    :gutters="false"
    class="orangehrm-directory-card"
  >
    <div
      v-show="showBackButton"
      class="orangehrm-directory-card-top"
      @click="$emit('hide-details', false)"
    >
      <oxd-icon name="arrow-right"></oxd-icon>
    </div>
    <div :class="nameClasses">
      <oxd-text type="toast-title">
        {{ employeeName }}
      </oxd-text>
    </div>
    <profile-picture :id="employeeId"></profile-picture>
    <div v-if="employeeDesignation" :class="designationClasses">
      <oxd-text type="toast-message">
        {{ employeeDesignation }}
      </oxd-text>
    </div>
    <div v-show="employeeSubUnit && employeeLocation" :class="bodyClasses">
      <span class="orangehrm-directory-card-icon">
        <oxd-icon name="geo-alt-fill"></oxd-icon>
      </span>
      <span>
        <div :class="subunitClasses">
          <oxd-text type="toast-message">
            {{ employeeSubUnit }}
          </oxd-text>
        </div>
        <div :class="locationClasses">
          <oxd-text type="toast-message">
            {{ employeeLocation }}
          </oxd-text>
        </div>
      </span>
    </div>
    <slot></slot>
  </oxd-sheet>
</template>

<script>
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import ProfilePicture from '@/orangehrmCorporateDirectoryPlugin/components/ProfilePicture';

export default {
  name: 'SummaryCard',
  components: {
    'oxd-sheet': Sheet,
    'oxd-icon': Icon,
    'profile-picture': ProfilePicture,
  },
  props: {
    employeeId: {
      type: Number,
      required: true,
    },
    employeeName: {
      type: String,
      required: true,
    },
    employeeDesignation: {
      type: String,
      required: false,
      default: null,
    },
    employeeSubUnit: {
      type: String,
      default: '',
    },
    employeeLocation: {
      type: String,
      default: '',
    },
    showBackButton: {
      type: Boolean,
      default: false,
    },
    employeeOnMobile: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['hide-details'],
  computed: {
    hasDefaultSlot() {
      return !!this.$slots.default;
    },
    addClassesOrNot() {
      return this.showBackButton
        ? !this.showBackButton
        : !this.employeeOnMobile;
    },
    bodyClasses() {
      return {
        'orangehrm-directory-card-body': true,
        'orangehrm-directory-card-body-change-height': this.addClassesOrNot,
      };
    },
    nameClasses() {
      return {
        'orangehrm-directory-card-name': true,
        'orangehrm-directory-card-name-change-height': this.addClassesOrNot,
        '--break-words': this.addClassesOrNot,
      };
    },
    designationClasses() {
      return {
        'orangehrm-directory-card-designation': true,
        'orangehrm-directory-card-designation-change-height': this
          .addClassesOrNot,
        '--break-words': this.addClassesOrNot,
      };
    },
    subunitClasses() {
      return {
        'orangehrm-directory-card-subunit': true,
        '--break-words': this.addClassesOrNot,
      };
    },
    locationClasses() {
      return {
        'orangehrm-directory-card-location': true,
        '--break-words': this.addClassesOrNot,
      };
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-directory-card {
  height: auto;
  overflow: hidden;
  padding: 0.5rem 1rem;
  @include oxd-respond-to('md') {
    min-height: 280px;
  }

  &-height {
    height: auto;
    @include oxd-respond-to('md') {
      height: 256px;
      overflow: hidden;
    }
  }

  &-name {
    padding-top: 1rem;
    padding-bottom: 0.75rem;
    text-align: center;
    justify-content: space-between;
    height: auto;

    &-change-height {
      height: 28px;
    }
  }

  &-designation {
    padding-top: 1rem;
    padding-bottom: 1rem;
    text-align: center;
    justify-content: space-between;
    height: auto;

    &-change-height {
      height: 20px;
    }
  }

  &-body {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem;
    margin-top: -0.25rem;
    background-color: $oxd-background-white-shadow-color;
    border-radius: 0.5rem;
    height: auto;

    &-change-height {
      height: 72px;
    }

    min-width: 168px;
  }

  &-icon {
    margin: 0 0.25rem 0 0;
    color: $oxd-interface-gray-darken-1-color;
    font-size: 24px;
    display: flex;
    justify-content: center;
  }

  &-subunit {
    margin-top: 0.25rem;
    margin-bottom: 0.5rem;
  }

  &-location {
    margin-top: 0.5rem;
    margin-bottom: 0.25rem;
  }
}

.--break-words {
  overflow: hidden;
  word-break: break-all;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
