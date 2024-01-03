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
  <oxd-sheet :gutters="false" class="orangehrm-directory-card">
    <div
      v-show="showBackButton"
      class="orangehrm-directory-card-top"
      @click="$emit('hide-details', false)"
    >
      <oxd-icon name="arrow-right"></oxd-icon>
    </div>
    <oxd-text tag="p" :class="cardTitleClasses">
      {{ employeeName }}
    </oxd-text>
    <profile-picture :id="employeeId"></profile-picture>
    <oxd-text v-show="employeeDesignation" tag="p" :class="cardSubTitleClasses">
      {{ employeeDesignation }}
    </oxd-text>
    <div
      v-show="employeeSubUnit || employeeLocation"
      class="orangehrm-directory-card-body"
    >
      <oxd-icon
        class="orangehrm-directory-card-icon"
        name="geo-alt-fill"
      ></oxd-icon>
      <div>
        <oxd-text
          v-show="employeeSubUnit"
          tag="p"
          :class="cardDescriptionClasses"
        >
          {{ employeeSubUnit }}
        </oxd-text>
        <oxd-text
          v-show="employeeLocation"
          tag="p"
          :class="cardDescriptionClasses"
        >
          {{ employeeLocation }}
        </oxd-text>
      </div>
    </div>
    <slot></slot>
  </oxd-sheet>
</template>

<script>
import ProfilePicture from '@/orangehrmCorporateDirectoryPlugin/components/ProfilePicture';
import {OxdIcon, OxdSheet} from '@ohrm/oxd';

export default {
  name: 'SummaryCard',
  components: {
    'oxd-icon': OxdIcon,
    'oxd-sheet': OxdSheet,
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
  },
  emits: ['hide-details'],
  computed: {
    hasDefaultSlot() {
      return !!this.$slots.default;
    },
    cardTitleClasses() {
      return {
        'orangehrm-directory-card-header': true,
        '--break-words': !this.hasDefaultSlot,
      };
    },
    cardSubTitleClasses() {
      return {
        'orangehrm-directory-card-subtitle': true,
        '--break-words': !this.hasDefaultSlot,
      };
    },
    cardDescriptionClasses() {
      return {
        'orangehrm-directory-card-description': true,
        '--break-words': !this.hasDefaultSlot,
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-directory-card {
  height: auto;
  cursor: pointer;
  overflow: hidden;
  padding: 0.5rem 1rem;

  &-header {
    font-size: 14px;
    min-height: 28px;
    font-weight: 700;
    text-align: center;
    margin-top: 1rem;
    margin-bottom: 0.75rem;
    word-break: break-word;
    &.--break-words {
      @include truncate(2, 1, #fff);
    }
  }

  &-subtitle {
    font-size: 12px;
    font-weight: 700;
    text-align: center;
    margin-top: 1rem;
    margin-bottom: 0.75rem;
    word-break: break-word;
    &.--break-words {
      @include truncate(1, 1, #fff);
    }
  }

  &-description {
    font-size: 12px;
    text-align: left;
    word-break: break-word;
    &.--break-words {
      @include truncate(1, 1, #fff);
    }
    &:first-of-type {
      margin-bottom: 0.25rem;
    }
  }

  &-body {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border-radius: 0.5rem;
    background-color: $oxd-background-white-shadow-color;
  }

  &-icon {
    font-size: 24px;
    margin-right: 0.5rem;
    color: $oxd-interface-gray-darken-1-color;
  }

  @include oxd-respond-to('md') {
    min-height: 260px;
  }
}
</style>
