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
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('general.add_attachment') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="attachment.attachment"
              type="file"
              :label="$t('general.select_file')"
              :button-label="$t('general.browse')"
              :rules="rules.attachment"
              :placeholder="$t('general.no_file_selected')"
              required
            />
            <oxd-text class="orangehrm-input-hint" tag="p">
              {{ $t('general.accepts_up_to_n_mb', {count: formattedFileSize}) }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="attachment.description"
              type="textarea"
              :label="$t('general.comment')"
              :placeholder="$t('general.type_comment_here')"
              :rules="rules.description"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {required} from '@/core/util/validation/rules';
import {OxdDialog} from '@ohrm/oxd';
import {
  maxFileSize,
  shouldNotExceedCharLength,
  validFileTypes,
} from '@ohrm/core/util/validation/rules';

const attachmentModel = {
  attachment: null,
  description: '',
};

export default {
  name: 'SaveAttachment',

  components: {
    'oxd-dialog': OxdDialog,
  },

  props: {
    requestId: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },

  emits: ['close'],

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/claim/requests/${props.requestId}/attachments`,
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      attachment: {
        ...attachmentModel,
      },
      rules: {
        description: [shouldNotExceedCharLength(200)],
        attachment: [
          required,
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
      },
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
        .then(() => {
          return this.$toast.saveSuccess();
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
};
</script>
