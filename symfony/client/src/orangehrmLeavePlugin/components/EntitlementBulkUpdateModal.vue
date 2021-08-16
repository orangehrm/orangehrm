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
  <oxd-dialog
    v-if="show"
    :gutters="false"
    :style="{width: '90%', maxWidth: '600px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-header-container">
      <oxd-text type="card-title">
        {{ $t('leave.updating_entitlement') }} -
        {{ $t('leave.matching_employees') }}
      </oxd-text>
    </div>
    <oxd-divider class="orangehrm-horizontal-margin orangehrm-clear-margins" />
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text type="subtitle-2">
        The selected leave entitlement will be applied to the following
        employees.
      </oxd-text>
    </div>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items"
        :clickable="false"
        class="orangehrm-horizontal-padding"
        rowDecorator="oxd-table-decorator-card"
      />
    </div>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-form-actions>
        <oxd-button
          displayType="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button :label="$t('general.confirm')" @click="onConfirm" />
      </oxd-form-actions>
    </div>
  </oxd-dialog>
</template>

<script>
import Dialog from '@orangehrm/oxd/core/components/Dialog/Dialog';

export default {
  name: 'entitlement-bulk-update-modal',
  components: {
    'oxd-dialog': Dialog,
  },
  data() {
    return {
      show: false,
      reject: null,
      resolve: null,
      headers: [
        {
          title: 'Employee',
          name: 'employee',
          slot: 'title',
          style: {flex: 1},
        },
        {
          title: 'Old Entitlement',
          name: 'old',
          style: {flex: 1},
        },
        {
          title: 'New Entitlement',
          name: 'new',
          style: {flex: 1},
        },
      ],
      items: [
        {employee: 'Sam Jackson', old: '1.00', new: '5.00'},
        {employee: 'Micheal Knight', old: '12.00', new: '14.00'},
        {employee: 'Kevin Peterson', old: '0.00', new: '0.50'},
      ],
    };
  },
  methods: {
    showDialog() {
      return new Promise((resolve, reject) => {
        this.resolve = resolve;
        this.reject = reject;
        this.show = true;
      });
    },
    onConfirm() {
      this.show = false;
      this.resolve && this.resolve('ok');
    },
    onCancel() {
      this.show = false;
      this.resolve && this.resolve('cancel');
    },
  },
};
</script>

<style scoped>
.oxd-overlay {
  z-index: 1100 !important;
}
</style>
