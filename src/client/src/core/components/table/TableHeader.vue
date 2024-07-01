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
  <div v-if="!loading">
    <oxd-divider v-if="showDivider" class="orangehrm-horizontal-margin" />
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <div v-if="selected > 0">
        <oxd-text tag="span">
          {{ $t('general.n_records_selected', {count: selected}) }}
        </oxd-text>
        <oxd-button
          :label="$t('general.delete_selected')"
          icon-name="trash-fill"
          display-type="label-danger"
          class="orangehrm-horizontal-margin"
          @click="$emit('delete', $event)"
        />
      </div>
      <oxd-text v-else tag="span">
        {{ $t(recordsFoundLangString, {count: total}) }}
      </oxd-text>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TableHeader',

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
    showDivider: {
      type: Boolean,
      required: false,
      default: true,
    },
    recordsFoundLangString: {
      type: String,
      required: false,
      default: 'general.n_records_found',
    },
  },

  emits: ['delete'],
};
</script>

<style scoped>
.oxd-divider {
  margin-top: 0;
  margin-bottom: 0;
}
</style>
