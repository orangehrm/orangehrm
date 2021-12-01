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
      <div class="orangehrm-information-card-container">
        <oxd-text class="orangehrm-sub-title">Note:</oxd-text>
        <ul>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              Column order should not be changed
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              First Name and Last Name are compulsory
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              All date fields should be in YYYY-MM-DD format
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              If gender is specified, value should be either Male or Female
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              Each import file should be configured for 100 records or less
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              Multiple import files may be required
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              Sample CSV file:
              <a
                class="download-link"
                href="#"
                @click.prevent="onClickDownload"
              >
                Download
              </a>
            </oxd-text>
          </li>
        </ul>
      </div>

      <oxd-form ref="formRef" :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="attachment.attachment"
                type="file"
                label="Select File"
                button-label="Browse"
                :rules="rules.attachment"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                Accepts up to 1MB
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <submit-button label="Upload" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  maxFileSize,
  validFileTypes,
} from '@/core/util/validation/rules';
import useForm from '@ohrm/core/util/composable/useForm';

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
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/pim/csv-import`,
    );
    const {formRef, reset} = useForm();
    return {
      http,
      reset,
      formRef,
    };
  },

  data() {
    return {
      isLoading: false,
      attachment: {
        ...attachmentModel,
      },
      rules: {
        attachment: [
          required,
          maxFileSize(1048576),
          validFileTypes(this.allowedFileTypes),
        ],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.attachment,
        })
        .then(response => {
          const importedRecords = response.data.meta.total;
          this.reset();
          this.isLoading = false;
          if (importedRecords > 0) {
            return this.$toast.success({
              title: 'Success',
              message: 'Number of Records Imported: ' + importedRecords,
            });
          }
          return this.$toast.error({
            title: 'Failed to Import',
            message: 'No Records Added',
          });
        });
    },
    onClickDownload() {
      const downUrl = `${window.appGlobal.baseUrl}/pim/sampleCsvDownload`;
      window.open(downUrl, '_blank');
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-information-card-container {
  background-color: $oxd-interface-gray-lighten-2-color;
  border-radius: 1.2rem;
  padding: 1.2rem;
}
.orangehrm-information-card-text {
  font-size: $oxd-input-control-font-size;
  color: $oxd-input-control-font-color;
  font-weight: $oxd-input-control-font-weight;
  & .download-link {
    color: $oxd-primary-one-color;
  }
}
</style>
