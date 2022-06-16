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
    <div class="orangehrm-directory-card-header">
      <oxd-text type="card-title">
        {{ employeeName }}
      </oxd-text>
    </div>
    <profile-picture :id="employeeId"></profile-picture>
    <div v-show="employeeDesignation" class="orangehrm-directory-card-header">
      <oxd-text type="toast-title">
        {{ employeeDesignation }}
      </oxd-text>
    </div>
    <div
      v-show="employeeSubUnit || employeeLocation"
      class="orangehrm-directory-card-body"
    >
      <span class="orangehrm-directory-card-icon">
        <oxd-icon name="geo-alt-fill"></oxd-icon>
      </span>
      <span>
        <div class="orangehrm-directory-card-subunit">
          <oxd-text type="toast-message">
            {{ employeeSubUnit }}
          </oxd-text>
        </div>
        <div class="orangehrm-directory-card-location">
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
      required: true,
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
  },
  emits: ['hide-details'],
  computed: {
    hasDefaultSlot() {
      return !!this.$slots.default;
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
      height: 260px;
      overflow: hidden;
    }
  }

  &-header {
    padding-top: 1rem;
    padding-bottom: 0.75rem;
    text-align: center;
    justify-content: space-between;
    height: 32px;
    word-break: break-all;
    -webkit-line-clamp: 2;
    text-overflow: ellipsis;
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
  }

  &-body {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem;
    margin-top: -0.25rem;
    background-color: $oxd-background-white-shadow-color;
    border-radius: 0.5rem;
    height: 64px;
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
    margin-bottom: 0.25rem;
  }

  &-location {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
  }
}
</style>
