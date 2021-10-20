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
    <oxd-input-field
      type="select"
      label="&nbsp;"
      :rules="rules.operator"
      :options="operators"
      :modelValue="operator"
      @update:modelValue="$emit('update:operator', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item
    v-if="operator && operator.id === 'between'"
    class="orangehrm-report-range"
  >
    <oxd-input-field
      label="&nbsp;"
      :type="type"
      :rules="rules.valueX"
      :modelValue="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
    <oxd-text class="orangehrm-report-range-text" tag="p">to</oxd-text>
    <oxd-input-field
      label="&nbsp;"
      :type="type"
      :rules="rules.valueX"
      :modelValue="valueY"
      @update:modelValue="$emit('update:valueY', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item v-else-if="operator">
    <oxd-input-field
      label="&nbsp;"
      :type="type"
      :rules="rules.valueY"
      :modelValue="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
  </oxd-grid-item>
</template>

<script>
import {ref} from 'vue';
import {required} from '@orangehrm/core/util/validation/rules';

export default {
  name: 'report-criterion-range',
  inheritAttrs: false,
  props: {
    operator: {
      type: Object,
      required: false,
    },
    valueX: {
      type: String,
      required: false,
    },
    valueY: {
      type: String,
      required: false,
    },
    type: {
      type: String,
      default: 'input',
    },
  },
  setup() {
    const operators = ref([
      {id: 'lt', label: 'Less Than'},
      {id: 'gt', label: 'Greater Than'},
      {id: 'between', label: 'Range'},
    ]);

    const rules = {
      operator: [required],
      valueX: [required],
      valueY: [required],
    };

    return {
      rules,
      operators,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-report {
  &-range {
    display: flex;
    justify-content: center;
    align-items: center;
  }
  &-range-text {
    margin: 0 1rem;
    font-size: $oxd-input-control-font-size;
  }
}
</style>
