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
      <oxd-text class="orangehrm-main-title">Data Import</oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                  type="file"
                  label="Select File"
                  buttonLabel="Browse"
                  v-model="attachment.attachment"
                  :rules="rules.attachment"
                  required
              />
              <oxd-text class="orangehrm-input-hint" tag="p"
              >Accepts up to 1MB</oxd-text
              >
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            class="orangehrm-left-space"
            displayType="secondary"
            label="Upload"
            type="submit"
        />

        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from "@/core/util/services/api.service";

const attachmentModel = {
  attachment: null,
};

export default {

  props: {
    allowedFileTypes: {
      type: Array,
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
        attachment: [
          v => {
            return v !== null || 'Required';
          },
          v =>
              (v && v.size && v.size <= 1024 * 1024) ||
              'Attachment size exceeded',
          v =>
              (v &&
                  this.allowedFileTypes.findIndex(item => item === v.type) > -1) ||
              'File type not allowed',
        ],
      },
    };
  },

  components: {},

  setup(props) {
    const http = new APIService(
        window.appGlobal.baseUrl,
        `api/v2/pim/csvImport`,
    );

    return {
      http,
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
          .create({
            ...this.attachment,
          })
          .then(() => {
            this.updateModel();
            return this.$toast.saveSuccess();
          })
          .then(() => {
            this.isLoading = false;
          });
    },
    updateModel() {
      this.attachmentModel = {attachment: null};
    },
  },

  created() {
    this.isLoading = false;
  },
};
</script>

<style lang="scss" scoped>
</style>
