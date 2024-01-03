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
  <div class="orangehrm-header-container">
    <div v-if="bulkActions && selected > 0" class="actions">
      <oxd-text tag="span">
        {{ $t('general.n_records_selected', {count: selected}) }}
      </oxd-text>
      <oxd-button
        v-if="bulkActions.APPROVE"
        :label="$t('general.approve')"
        display-type="label-success"
        @click="$emit('onActionClick', 'APPROVE')"
      />
      <oxd-button
        v-if="bulkActions.REJECT"
        :label="$t('general.reject')"
        display-type="label-danger"
        @click="$emit('onActionClick', 'REJECT')"
      />
      <oxd-button
        v-if="bulkActions.CANCEL"
        :label="$t('general.cancel')"
        display-type="label-warn"
        @click="$emit('onActionClick', 'CANCEL')"
      />
    </div>
    <oxd-text v-else tag="span">
      {{ $t('general.n_records_found', {count: total}) }}
    </oxd-text>
  </div>
</template>

<script>
export default {
  name: 'LeaveListTableHeader',

  props: {
    loading: {
      type: Boolean,
      required: true,
    },
    selected: {
      type: Number,
      required: true,
    },
    total: {
      type: Number,
      required: true,
    },
    bulkActions: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },

  emits: ['onActionClick'],
};
</script>

<style lang="scss" scoped>
.orangehrm-header-container {
  .actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
  }
  span {
    margin-right: 20px;
  }
}
</style>
