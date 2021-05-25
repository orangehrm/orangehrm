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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6">Save Attachment</oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            type="file"
            label="Select File"
            buttonLabel="Browse"
            v-model="attachment.file"
            :rules="rules.file"
          />
          <oxd-text class="orangehrm-input-hint" tag="p"
            >Accepts up to 1MB</oxd-text
          >
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            type="textarea"
            label="Comment"
            placeholder="Type comment here"
            v-model="attachment.comment"
            :rules="rules.comment"
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
    </div>
  </div>
</template>

<script>
const attachmentModel = {
  file: null,
  comment: '',
};

export default {
  name: 'save-attachment',

  props: {
    http: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      attachment: {
        ...attachmentModel,
      },
      rules: {
        comment: [
          v =>
            (v && v.length <= 400) ||
            v === '' ||
            'Should not exceed 400 characters',
        ],
        file: [
          v => {
            return v !== null || 'Required';
          },
          v =>
            (v && v.size && v.size <= 1024 * 1024) ||
            'Attachment size exceeded',
        ],
      },
    };
  },

  methods: {
    // TODO: API Call
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.attachment,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Added',
          });
        })
        .then(() => {
          this.attachment = {...attachmentModel};
          this.isLoading = false;
          this.onCancel();
        });
    },
    onCancel() {
      console.log('on cancel');
    },
  },
};
</script>
