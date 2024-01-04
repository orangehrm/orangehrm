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
  <oxd-grid-item>
    <employee-autocomplete
      v-bind="$attrs"
      :params="{
        includeEmployees: 'currentAndPast',
      }"
      :rules="rules"
      :model-value="valueX"
      @update:model-value="$emit('update:valueX', $event)"
    ></employee-autocomplete>
  </oxd-grid-item>
</template>

<script>
import {required, validSelection} from '@ohrm/core/util/validation/rules';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';

export default {
  name: 'ReportCriterionAutocomplete',

  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },
  inheritAttrs: false,

  props: {
    operator: {
      type: Object,
      required: false,
      default: () => null,
    },
    valueX: {
      type: Object,
      required: false,
      default: () => null,
    },
  },
  emits: ['update:valueX', 'update:operator'],
  setup(_, context) {
    const rules = [required, validSelection];
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
