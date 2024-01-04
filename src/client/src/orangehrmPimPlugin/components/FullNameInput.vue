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
  <!-- Always use inside OXD-Form -->
  <oxd-input-group :label="localizedLabel" :classes="classes">
    <oxd-input-field
      class="orangehrm-firstname"
      name="firstName"
      :placeholder="$t('general.first_name')"
      :model-value="firstName"
      :rules="rules.firstName"
      :disabled="disabled"
      @update:model-value="$emit('update:firstName', $event)"
    />
    <oxd-input-field
      class="orangehrm-middlename"
      name="middleName"
      :model-value="middleName"
      :rules="rules.middleName"
      :disabled="disabled"
      :placeholder="showMiddleNamePlaceholder ? $t('general.middle_name') : ''"
      @update:model-value="$emit('update:middleName', $event)"
    />
    <oxd-input-field
      class="orangehrm-lastname"
      name="lastName"
      :placeholder="$t('general.last_name')"
      :model-value="lastName"
      :rules="rules.lastName"
      :disabled="disabled"
      @update:model-value="$emit('update:lastName', $event)"
    />
  </oxd-input-group>
</template>

<script>
export default {
  name: 'FullNameInput',
  inheritAttrs: false,
  props: {
    firstName: {
      type: String,
      required: true,
    },
    middleName: {
      type: String,
      required: true,
    },
    lastName: {
      type: String,
      required: true,
    },
    rules: {
      type: Object,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    showMiddleNamePlaceholder: {
      type: Boolean,
      default: true,
    },
    label: {
      type: String,
      default: null,
    },
  },
  emits: ['update:firstName', 'update:middleName', 'update:lastName'],
  computed: {
    classes() {
      return {
        label: {
          'oxd-input-field-required': true,
        },
        wrapper: {
          '--name-grouped-field': true,
        },
      };
    },
    localizedLabel() {
      return this.label ? this.label : this.$t('general.employee_full_name');
    },
  },
};
</script>

<style lang="scss" scoped>
@include oxd-respond-to('md') {
  ::v-deep(.--name-grouped-field) {
    display: flex;
  }

  ::v-deep(.orangehrm-firstname) {
    border-bottom-right-radius: unset;
    border-top-right-radius: unset;
    text-overflow: ellipsis;
  }

  ::v-deep(.orangehrm-lastname) {
    border-bottom-left-radius: unset;
    border-top-left-radius: unset;
    text-overflow: ellipsis;
  }

  ::v-deep(.orangehrm-middlename) {
    border-radius: unset;
    text-overflow: ellipsis;
  }
}
</style>
