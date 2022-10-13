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
  <teleport to="#app">
    <oxd-dialog
      v-if="show"
      class="orangehrm-confirmation-dialog orangehrm-dialog-popup"
      style="width: 450px"
      @update:show="onCancel"
    >
      <div class="orangehrm-modal-header">
        <oxd-text type="card-title">{{ $t('pim.import_details') }}</oxd-text>
      </div>
      <div
        class="orangehrm-text-center-align"
        style="overflow-wrap: break-word"
      >
        <oxd-text type="card-body">
          {{ $t('pim.n_records_successfully_imported', {count: success}) }}
        </oxd-text>
        <oxd-text v-if="failed > 0" type="card-body">
          {{ $t('pim.n_records_failed_to_import', {count: failed}) }}
        </oxd-text>
        <oxd-text v-if="failed > 0" type="card-body">
          {{ $t('pim.failed_rows') }}
        </oxd-text>
        <oxd-text v-if="failed > 0" type="card-body">
          {{ failedRows.toString() }}
        </oxd-text>
      </div>
      <div class="orangehrm-modal-footer">
        <oxd-button
          :label="$t('general.ok')"
          display-type="text"
          class="orangehrm-button-margin"
          @click="onCancel"
        />
      </div>
    </oxd-dialog>
  </teleport>
</template>

<script>
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';

export default {
  name: 'EmployeeImportDetailsDialog',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    success: {
      type: Number,
      required: true,
    },
    failed: {
      type: Number,
      required: true,
    },
    failedRows: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      show: false,
      reject: null,
      resolve: null,
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

<style
  src="../../core/components/dialogs/dialog.scss"
  lang="scss"
  scoped
></style>
