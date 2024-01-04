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
    <oxd-input-field
      type="select"
      :rules="rules.operator"
      :options="operators"
      :model-value="operator"
      @update:model-value="$emit('update:operator', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item
    v-if="operator && operator.id === 'between'"
    class="orangehrm-report-daterange --span-column-2"
  >
    <oxd-input-field
      type="date"
      :rules="rules.valueX"
      :model-value="valueX"
      :placeholder="userDateFormat"
      :display-format="jsDateFormat"
      @update:model-value="$emit('update:valueX', $event)"
    />
    <oxd-text class="orangehrm-report-range-text" tag="p">to</oxd-text>
    <oxd-input-field
      type="date"
      :rules="rules.valueY"
      :model-value="valueY"
      :placeholder="userDateFormat"
      :display-format="jsDateFormat"
      @update:model-value="$emit('update:valueY', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item v-else-if="operator">
    <oxd-input-field
      type="date"
      :rules="rules.valueXOnly"
      :model-value="valueX"
      :placeholder="userDateFormat"
      :display-format="jsDateFormat"
      @update:model-value="$emit('update:valueX', $event)"
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
import usei18n from '@/core/util/composable/usei18n';
import useDateFormat from '@/core/util/composable/useDateFormat';

export default {
  name: 'ReportCriterionDateRange',
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
    const {jsDateFormat, userDateFormat} = useDateFormat();
    const operators = ref([
      {id: 'lt', label: 'Joined before'},
      {id: 'gt', label: 'Joined after'},
      {id: 'between', label: 'Joined in between'},
    ]);

    const rules = {
      operator: [required],
      valueXOnly: [required, validDateFormat(userDateFormat)],
      valueX: [
        required,
        validDateFormat(userDateFormat),
        startDateShouldBeBeforeEndDate(
          () => props.valueY,
          $t('general.from_date_should_be_before_to_date'),
        ),
      ],
      valueY: [
        required,
        validDateFormat(userDateFormat),
        endDateShouldBeAfterStartDate(
          () => props.valueX,
          $t('general.to_date_should_be_after_from_date'),
        ),
      ],
    };

    return {
      rules,
      operators,
      jsDateFormat,
      userDateFormat,
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
