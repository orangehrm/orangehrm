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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text class="orangehrm-main-title">
        {{ $t('pim.data_import') }}
      </oxd-text>

      <oxd-divider />
      <div class="orangehrm-information-card-container">
        <oxd-text class="orangehrm-sub-title">
          {{ $t('general.note') }}:
        </oxd-text>
        <ul>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('pim.column_order_should_not_be_changed') }}
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('pim.first_name_and_last_name_are_compulsory') }}
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('pim.all_date_fields_should_be_in_yyyy_mm_dd_format') }}
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('pim.gender_specified_value_should_be_either_m_or_f') }}
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{
                $t(
                  'pim.each_import_file_should_be_configured_for_100_records_or_less',
                )
              }}
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('pim.multiple_import_files_may_be_required') }}
            </oxd-text>
          </li>
          <li>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('pim.sample_csv_file') }} :
              <a
                href="#"
                class="download-link"
                @click.prevent="onClickDownload"
              >
                {{ $t('general.download') }}
              </a>
            </oxd-text>
          </li>
        </ul>
      </div>
      <br />

      <oxd-form ref="formRef" :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="attachment.attachment"
                type="file"
                :rules="rules.attachment"
                :label="$t('general.select_file')"
                :button-label="$t('general.browse')"
                :placeholder="$t('general.no_file_selected')"
                required
              />
              <oxd-text class="orangehrm-input-hint" tag="p">
                {{
                  $t('general.accepts_up_to_n_mb', {count: formattedFileSize})
                }}
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <submit-button :label="$t('general.upload')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <employee-data-import-modal
      v-if="importModalState"
      :data="importModalState"
      @close="onImportModalClose"
    ></employee-data-import-modal>
  </div>
</template>

<script>
import {
  required,
  maxFileSize,
  validFileTypes,
} from '@/core/util/validation/rules';
import useForm from '@ohrm/core/util/composable/useForm';
import {APIService} from '@/core/util/services/api.service';
import EmployeeDataImportModal from '@/orangehrmPimPlugin/components/EmployeeDataImportModal';

const attachmentModel = {
  attachment: null,
};

export default {
  components: {
    'employee-data-import-modal': EmployeeDataImportModal,
  },
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/csv-import`,
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
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
      },
      importModalState: null,
    };
  },
  computed: {
    formattedFileSize() {
      return Math.round((this.maxFileSize / (1024 * 1024)) * 100) / 100;
    },
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.attachment,
        })
        .then((response) => {
          const {meta} = response.data;
          this.importModalState = meta;
        })
        .finally(() => {
          this.reset();
          this.isLoading = false;
        });
    },
    onClickDownload() {
      const downUrl = `${window.appGlobal.baseUrl}/pim/sampleCsvDownload`;
      window.open(downUrl, '_blank');
    },
    onImportModalClose() {
      this.importModalState = null;
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
