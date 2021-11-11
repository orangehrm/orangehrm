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
      :modelValue="operator"
      @update:modelValue="$emit('update:operator', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item
    v-if="operator && operator.id === 'between'"
    class="orangehrm-report-daterange --span-column-2"
  >
    <oxd-input-field
      type="date"
      placeholder="yyyy-mm-dd"
      :rules="rules.valueX"
      :modelValue="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
    <oxd-text class="orangehrm-report-range-text" tag="p">to</oxd-text>
    <oxd-input-field
      type="date"
      placeholder="yyyy-mm-dd"
      :rules="rules.valueY"
      :modelValue="valueY"
      @update:modelValue="$emit('update:valueY', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item v-else-if="operator">
    <oxd-input-field
      type="date"
      placeholder="yyyy-mm-dd"
      :rules="rules.valueXOnly"
      :modelValue="valueX"
      @update:modelValue="$emit('update:valueX', $event)"
    />
  </oxd-grid-item>
</template>

<script>
import {ref} from 'vue';
import {
  required,
  validDateFormat,
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
} from '@ohrm/core/util/validation/rules';

export default {
  name: 'report-criterion-date-range',
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
  },
  setup(props) {
    const operators = ref([
      {id: 'lt', label: 'Joined before'},
      {id: 'gt', label: 'Joined after'},
      {id: 'between', label: 'Joined in between'},
    ]);

    const rules = {
      operator: [required],
      valueXOnly: [required, validDateFormat()],
      valueX: [
        required,
        validDateFormat(),
        startDateShouldBeBeforeEndDate(
          () => props.valueY,
          'From date should be before to date',
        ),
      ],
      valueY: [
        required,
        validDateFormat(),
        endDateShouldBeAfterStartDate(
          () => props.valueX,
          'To date should be after from date',
        ),
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
  &-daterange {
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
