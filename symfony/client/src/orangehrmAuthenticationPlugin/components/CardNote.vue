<!--
  - OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  - all the essential functionalities required for any enterprise.
  - Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
  -
  - OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  - the GNU General Public License as published by the Free Software Foundation; either
  - version 2 of the License, or (at your option) any later version.
  -
  - OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  - without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  - See the GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License along with this program;
  - if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  - Boston, MA  02110-1301, USA
  -->

<template>
  <oxd-text
    :class="{
      'orangehrm-forgot-password-note': true,
      'orangehrm-forgot-password-note--background': note,
      'orangehrm-forgot-password-note--icon': noteIcon,
    }"
  >
    <oxd-text v-if="note" class="orangehrm-forgot-password-note-header"
      >Note:</oxd-text
    >
    <oxd-text
      v-if="noteIcon"
      tag="span"
      class="orangehrm-forgot-password-note-icon"
    >
      <oxd-icon
        name="exclamation-triangle"
        class="orangehrm-forgot-password-icon"
      />
    </oxd-text>
    <oxd-text :class="{'orangehrm-forgot-password-note-text': noteIcon}">
      {{ noteText }}</oxd-text
    >
  </oxd-text>
</template>

<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon.vue';
export default {
  name: 'CardNote',
  components: {
    'oxd-icon': Icon,
  },
  props: {
    noteText: {
      type: String,
      required: true,
    },
    note: {
      type: Boolean,
      default: false,
    },
    noteIcon: {
      type: Boolean,
      required: false,
    },
  },
};
</script>

<style scoped lang="scss">
@import '@ohrm/oxd/styles/_colors.scss';
@import '@ohrm/oxd/styles/_variables.scss';
.orangehrm-forgot-password {
  &-icon {
    font-size: 1.5rem;
    color: $oxd-feedback-danger-color;
    padding: 1rem;
  }
  &-note {
    &-text {
      color: $oxd-feedback-danger-color;
    }
  }
  &-note--background {
    background-color: $oxd-interface-gray-lighten-2-color;
    border-radius: $oxd-border-radius;
    padding: 1.2rem;
  }
  &-note--icon {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: rgb(250, 234, 234);
    border-radius: $oxd-border-radius;
    padding: 1rem;
  }
  &-note-header {
    padding: 0.25rem 0 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
  }
}
</style>
