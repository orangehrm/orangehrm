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
      @update:show="onCancel"
      :style="{maxWidth: '450px'}"
    >
      <div class="orangehrm-modal-header">
        <oxd-text type="card-title"> {{ action }} Leave </oxd-text>
      </div>
      <div class="orangehrm-text-center-align">
        <oxd-text type="subtitle-2">
          You are about to {{ action }} to {{ count }} Leave Requests. Are you
          sure you want to continue?
        </oxd-text>
      </div>
      <div class="orangehrm-modal-footer">
        <oxd-button
          label="No, Cancel"
          displayType="text"
          class="orangehrm-button-margin"
          @click="onCancel"
        />
        <oxd-button
          label="Yes, Confirm"
          displayType="secondary"
          @click="onConfirm"
        />
      </div>
    </oxd-dialog>
  </teleport>
</template>

<script>
import Dialog from '@orangehrm/oxd/core/components/Dialog/Dialog';

export default {
  name: 'leave-bulk-action-modal',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    data: {
      type: Object,
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
    onCancel() {
      this.show = false;
      this.resolve && this.resolve('cancel');
    },
    onConfirm() {
      this.show = false;
      this.resolve && this.resolve('ok');
    },
  },
  computed: {
    count() {
      return this.data?.count ? this.data.count : 0;
    },
    action() {
      return this.data?.action === 'APPROVE'
        ? 'Approve'
        : this.data?.action === 'REJECT'
        ? 'Reject'
        : 'Cancel';
    },
  },
};
</script>

<style scoped>
.oxd-overlay {
  z-index: 1100 !important;
}
.orangehrm-modal-header {
  margin-bottom: 1.2rem;
  display: flex;
  justify-content: center;
}
.orangehrm-modal-footer {
  margin-top: 1.2rem;
  display: flex;
  justify-content: center;
}
.orangehrm-button-margin {
  margin: 0.25rem;
}
.orangehrm-text-center-align {
  text-align: center;
}
</style>
