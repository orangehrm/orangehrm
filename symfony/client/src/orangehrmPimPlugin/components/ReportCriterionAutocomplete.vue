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
  <oxd-grid-item>
    <employee-autocomplete
      v-bind="$attrs"
      :params="{
        includeEmployees: 'currentAndPast',
      }"
      :rules="rules"
      :modelValue="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    ></employee-autocomplete>
  </oxd-grid-item>
</template>

<script>
import {required} from '@orangehrm/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';

export default {
  name: 'report-criterion-autocomplete',
  inheritAttrs: false,

  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },

  props: {
    operator: {
      type: Object,
      required: false,
    },
    valueX: {
      type: Object,
      required: false,
    },
  },

  setup(_, context) {
    const rules = [required];
    context.emit('update:operator', {id: 'eq', label: 'Equal'});

    return {
      rules,
    };
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.oxd-input-group__label-wrapper) {
  display: none;
}
</style>
