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
  <oxd-dialog
    :style="{width: '90%', maxWidth: '450px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('general.comment') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-timesheet-grid">
          <oxd-text tag="p" class="orangehrm-timesheet-title">
            {{ $t('time.project') }}:
          </oxd-text>
          <oxd-text tag="p" class="orangehrm-timesheet-text">
            {{ data?.project.label }}
          </oxd-text>
          <oxd-text tag="p" class="orangehrm-timesheet-title">
            {{ $t('time.activity') }}:
          </oxd-text>
          <oxd-text tag="p" class="orangehrm-timesheet-text">
            {{ data?.activity.label }}
          </oxd-text>
          <oxd-text tag="p" class="orangehrm-timesheet-title">
            {{ $t('general.date') }}:
          </oxd-text>
          <oxd-text tag="p" class="orangehrm-timesheet-text">
            {{ data?.date }}
          </oxd-text>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="comment"
          type="textarea"
          placeholder="Comment here"
          :rules="rules.comment"
          :disabled="!editable"
        />
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions>
        <oxd-button
          type="button"
          display-type="ghost"
          label="Cancel"
          @click="onCancel"
        />
        <submit-button v-show="editable" />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

export default {
  name: 'TimesheetCommentModal',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    date: {
      type: String,
      required: false,
      default: null,
    },
    editable: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['close'],
  setup() {
    const http = new APIService(
      // window.appGlobal.baseUrl,
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      `api/v2/time/timesheet-comments`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      comment: null,
      data: null,
      rules: {
        comment: [required, shouldNotExceedCharLength(255)],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll({
        date: this.date,
      })
      .then(response => {
        const {data} = response.data;
        this.data = data;
        this.comment = data?.comment;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          comment: this.comment,
        })
        .then(() => {
          this.$toast.saveSuccess();
          this.onCancel();
        });
    },
    onCancel() {
      this.comment = null;
      this.$emit('close', true);
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-timesheet {
  &-grid {
    width: 100%;
    grid-template-columns: 100px 1fr;
    margin-bottom: 1rem;
  }
  &-title,
  &-text {
    word-break: break-word;
    font-size: $oxd-input-control-font-size;
  }
  &-title {
    font-weight: 700;
  }
}
</style>
