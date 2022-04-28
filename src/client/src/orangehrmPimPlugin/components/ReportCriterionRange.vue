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
      :rules="rules.operator"
      :options="operators"
      :model-value="operator"
      @update:modelValue="$emit('update:operator', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item
    v-if="operator && operator.id === 'between'"
    class="orangehrm-report-range"
  >
    <oxd-input-field
      :rules="rules.valueX"
      :model-value="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
    <oxd-text class="orangehrm-report-range-text" tag="p">to</oxd-text>
    <oxd-input-field
      :rules="rules.valueY"
      :model-value="valueY"
      @update:modelValue="$emit('update:valueY', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item v-else-if="operator">
    <oxd-input-field
      :rules="rules.valueXOnly"
      :model-value="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
  </oxd-grid-item>
</template>

<script>
import {ref} from 'vue';
import {required, max, digitsOnly} from '@ohrm/core/util/validation/rules';
import usei18n from '@/core/util/composable/usei18n';

export default {
  name: 'ReportCriterionRange',
  inheritAttrs: false,
  props: {
    operator: {
      type: Object,
      required: false,
      default: () => null,
    },
    valueX: {
      type: String,
      required: false,
      default: null,
    },
    valueY: {
      type: String,
      required: false,
      default: null,
    },
  },
  emits: ['update:valueX', 'update:valueY', 'update:operator'],
  setup(props) {
    const {$t} = usei18n();
    const operators = ref([
      {id: 'lt', label: $t('general.less_than')},
      {id: 'gt', label: $t('general.greater_than')},
      {id: 'between', label: $t('general.range')},
    ]);

    const rules = {
      operator: [required],
      valueXOnly: [required, digitsOnly, max(100)],
      valueX: [
        required,
        digitsOnly,
        max(100),
        v => {
          return (
            parseInt(v) < parseInt(props.valueY) ||
            $t('general.should_be_less_than_upper_bound')
          );
        },
      ],
      valueY: [
        required,
        digitsOnly,
        max(100),
        v => {
          return (
            parseInt(v) > parseInt(props.valueX) ||
            $t('general.should_be_greater_than_lower_bound')
          );
        },
      ],
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
    align-items: baseline;
  }
  &-range-text {
    margin: 0 1rem;
    font-size: $oxd-input-control-font-size;
  }
}
::v-deep(.oxd-input-group__label-wrapper) {
  display: none;
}
</style>
