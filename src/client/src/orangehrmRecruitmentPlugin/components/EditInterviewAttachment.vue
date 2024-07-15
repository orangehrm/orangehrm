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
  <div class="orangehrm-card-container">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('general.edit_attachment') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-group :label="$t('general.current_file')">
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
              v-model="attachment.attachment"
              type="file"
              :label="$t('general.replace_with')"
              :button-label="$t('general.browse')"
              :rules="rules.attachment"
              :placeholder="$t('general.no_file_selected')"
            />
            <oxd-text class="orangehrm-input-hint" tag="p">
              {{ $t('general.accepts_up_to_n_mb', {count: formattedFileSize}) }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="attachment.comment"
              type="textarea"
              :label="$t('general.comment')"
              :placeholder="$t('general.type_comment_here')"
              :rules="rules.comment"
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
  </div>
</template>

<script>
import {
  maxFileSize,
  shouldNotExceedCharLength,
  validFileTypes,
} from '@ohrm/core/util/validation/rules';

const attachmentModel = {
  attachment: null,
  comment: '',
};

export default {
  name: 'EditInterviewAttachment',

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      currentFile: '',
      attachment: {
        ...attachmentModel,
      },
      rules: {
        comment: [shouldNotExceedCharLength(200)],
        attachment: [
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

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then((response) => {
        const {data} = response.data;
        this.currentFile = data.attachment.fileName;
        this.attachment.comment = data.comment;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          comment: this.attachment.comment,
          currentAttachment:
            this.attachment.attachment !== null
              ? 'replaceCurrent'
              : 'keepCurrent',
          attachment:
            this.attachment.attachment !== null
              ? this.attachment.attachment
              : undefined,
        })
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
};
</script>
