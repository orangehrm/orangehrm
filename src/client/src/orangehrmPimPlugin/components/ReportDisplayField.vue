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
  <oxd-grid-item class="orangehrm-report-field --offset-column-1">
    <oxd-icon-button name="trash-fill" @click="onClickDelete" />
    <oxd-text class="orangehrm-report-field-name">
      {{ fieldGroup.label }}
    </oxd-text>
  </oxd-grid-item>

  <oxd-grid-item>
    <oxd-multiselect-chips
      :selected="selectedFields"
      @chip-removed="onRemoveSelected"
    ></oxd-multiselect-chips>
  </oxd-grid-item>

  <oxd-grid-item class="orangehrm-report-field">
    <oxd-text class="orangehrm-report-field-header" tag="p">
      {{ $t('general.include_header') }}
    </oxd-text>
    <oxd-switch-input
      :model-value="includeHeader"
      @update:model-value="$emit('update:includeHeader', $event)"
    />
  </oxd-grid-item>
</template>

<script>
import {OxdSwitchInput, OxdMultiSelectChips} from '@ohrm/oxd';

export default {
  name: 'ReportDisplayField',

  components: {
    'oxd-switch-input': OxdSwitchInput,
    'oxd-multiselect-chips': OxdMultiSelectChips,
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
    const onClickDelete = ($event) => {
      context.emit('delete', $event);
    };

    const onRemoveSelected = ($event) => {
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
