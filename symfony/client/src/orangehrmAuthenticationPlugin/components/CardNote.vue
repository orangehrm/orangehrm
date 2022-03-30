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
    <oxd-text v-if="note" class="orangehrm-forgot-password-note--header"
      >Note:</oxd-text
    >
    <oxd-text
      v-if="noteIcon"
      tag="span"
      class="orangehrm-forgot-password-note-header"
    >
      <oxd-icon name="triangle-fill" class="orangehrm-forgot-password-icon" />
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
.orangehrm-forgot-password {
  &-icon {
    font-size: 2rem;
    color: #f75f5f;
  }
  &-note {
    &-text {
      color: rgb(236, 87, 87);
    }
  }
  &-note--background {
    background-color: rgb(243, 243, 243);
    padding: 0.2rem 0.2rem;
  }
  &-note--icon {
    display: flex;
    align-items: center;
    background-color: rgb(250, 234, 234);
    padding: 0.2rem 0.2rem;
  }
  &-note-header {
    margin: 4px 1px;
    font-weight: 700;
  }
}
</style>
