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
  <oxd-dialog @update:show="onCancel" :style="{minWidth: '50%'}">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">Edit Organization Unit</oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field
          label="Unit Id"
          v-model="orgUnit.id"
          :rules="rules.id"
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          label="Name"
          v-model="orgUnit.name"
          :rules="rules.name"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          type="textarea"
          label="Description"
          placeholder="Type description here"
          v-model="orgUnit.description"
          :rules="rules.description"
        />
      </oxd-form-row>

      <oxd-divider />

      <oxd-form-actions>
        <oxd-button
          type="button"
          displayType="ghost"
          label="Cancel"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import Dialog from '@orangehrm/oxd/core/components/Dialog/Dialog';
const orgUnitModel = {
  id: '',
  name: '',
  description: '',
};
export default {
  name: 'edit-org-unit',
  props: {
    data: {
      type: Object,
    },
  },
  components: {
    'oxd-dialog': Dialog,
  },
  data() {
    return {
      show: false,
      isLoading: false,
      orgUnit: {...orgUnitModel},
      rules: {
        name: [
          v => {
            return (!!v && v.trim() !== '') || 'Required';
          },
        ],
      },
    };
  },
  methods: {
    onSave() {
      // TODO: API connection
      this.isLoading = true;
      setTimeout(() => {
        this.$toast
          .success({
            title: 'Success',
            message: 'Organization unit added successfully!',
          })
          .then(() => {
            this.isLoading = false;
            this.onCancel();
          });
      }, 2000);
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>

<style scoped>
.oxd-overlay {
  z-index: 1100 !important;
}
</style>
