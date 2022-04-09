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
  <oxd-grid-item class="orangehrm-report-field --offset-column-1">
    <oxd-icon-button name="trash-fill" @click="onClickDelete" />
    <oxd-text class="orangehrm-report-field-name">
      {{ fieldGroup.label }}
    </oxd-text>
  </oxd-grid-item>

  <oxd-grid-item>
    <oxd-mutliselect-chips
      :selected="selectedFields"
      @chipRemoved="onRemoveSelected"
    ></oxd-mutliselect-chips>
  </oxd-grid-item>

  <oxd-grid-item class="orangehrm-report-field">
    <oxd-text class="orangehrm-report-field-header" tag="p">
      {{ $t('general.include_header') }}
    </oxd-text>
    <oxd-switch-input
      :model-value="includeHeader"
      @update:modelValue="$emit('update:includeHeader', $event)"
    />
  </oxd-grid-item>
</template>

<script>
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import MultiSelectChips from '@ohrm/oxd/core/components/Input/MultiSelect/MultiSelectChips';

export default {
  name: 'ReportDisplayField',

  components: {
    'oxd-switch-input': SwitchInput,
    'oxd-mutliselect-chips': MultiSelectChips,
  },

  props: {
    fieldGroup: {
      type: Object,
      required: true,
    },
    selectedFields: {
      type: Array,
      default: () => [],
    },
    includeHeader: {
      type: Boolean,
      required: true,
    },
  },

  emits: ['delete', 'deleteChip', 'update:includeHeader'],

  setup(_, context) {
    const onClickDelete = $event => {
      context.emit('delete', $event);
    };

    const onRemoveSelected = $event => {
      context.emit('deleteChip', $event);
    };

    return {
      onClickDelete,
      onRemoveSelected,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-report {
  &-field {
    display: flex;
    align-items: center;
  }
  &-field-name {
    margin-left: 1rem;
    font-weight: 700;
    font-size: $oxd-input-control-font-size;
    padding: $oxd-input-control-vertical-padding 0rem;
  }
  &-field-header {
    font-size: $oxd-input-control-font-size;
    margin-right: 1rem;
  }
}
</style>
