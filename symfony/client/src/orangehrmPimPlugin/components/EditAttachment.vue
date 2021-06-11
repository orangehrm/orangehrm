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
  <div class="orangehrm-card-container">
    <oxd-text tag="h6" class="orangehrm-main-title">Edit Attachment</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-group label="Current File">
              <oxd-text tag="p">
                {{ currentFile }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              type="file"
              label="Replace With"
              buttonLabel="Browse"
              v-model="attachment.attachment"
              :rules="rules.attachment"
            />
            <oxd-text class="orangehrm-input-hint" tag="p"
              >Accepts up to 1MB</oxd-text
            >
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              type="textarea"
              label="Comment"
              placeholder="Type comment here"
              v-model="attachment.description"
              :rules="rules.description"
            />
          </oxd-grid-item>
        </oxd-grid>
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
</template>

<script>
const attachmentModel = {
  attachment: null,
  description: '',
};

export default {
  name: 'edit-attachment',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      currentFile: '',
      attachment: {
        ...attachmentModel,
      },
      rules: {
        description: [
          v =>
            (v && v.length <= 400) ||
            v === '' ||
            'Should not exceed 400 characters',
        ],
        attachment: [
          v =>
            v === null ||
            (v && v.size && v.size <= 1024 * 1024) ||
            'Attachment size exceeded',
          v =>
            v === null ||
            (v &&
              this.allowedFileTypes.findIndex(item => item === v.type) > -1) ||
            'File type not allowed',
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {...this.attachment})
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.attachment = {...attachmentModel};
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.currentFile = data.filename;
        this.attachment.description = data.description;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
