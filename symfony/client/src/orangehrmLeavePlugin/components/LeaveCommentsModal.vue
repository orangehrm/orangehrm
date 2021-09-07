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
    @update:show="onCancel"
    :style="{width: '90%', maxWidth: '600px'}"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('leave.leave_request_comments') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <div v-if="!isLoading" class="orangehrm-modal-content">
      <leave-comment
        v-for="comment in comments"
        :key="comment.id"
        :data="comment"
      ></leave-comment>
    </div>
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field
          type="textarea"
          placeholder="Comment here"
          v-model="comment"
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
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Dialog from '@orangehrm/oxd/core/components/Dialog/Dialog';
import {
  required,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';
import LeaveComment from '@/orangehrmLeavePlugin/components/LeaveComment';

export default {
  name: 'leave-comment-modal',
  props: {
    leaveId: {
      type: Number,
      required: false,
    },
  },
  components: {
    'oxd-dialog': Dialog,
    'leave-comment': LeaveComment,
  },
  setup(props) {
    const http = new APIService(
      // window.appGlobal.baseUrl,
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      `api/v2/leave/leave-comments/${props.leaveId}`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      comment: null,
      rules: {
        comment: [required, shouldNotExceedCharLength(400)],
      },
      comments: [],
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          comment: this.comment,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.comment = null;
      this.$emit('close', true);
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.comments = data;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style lang="scss" scoped>
@import '@orangehrm/oxd/styles/_mixins.scss';

.oxd-overlay {
  z-index: 1100 !important;
}
.orangehrm-modal-content {
  max-height: 200px;
  overflow: hidden auto;
  margin: 0.5rem 0;
  @include oxd-scrollbar();
}
</style>
