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
    :style="{width: '90%', maxWidth: '600px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('leave.leave_request_comments') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <div v-if="!isLoading" class="orangehrm-modal-content">
      <leave-comment
        v-for="comm in comments"
        :key="comm.id"
        :data="comm"
      ></leave-comment>
    </div>
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field
          v-model="comment"
          type="textarea"
          :placeholder="$t('general.comment_here')"
          :rules="rules.comment"
        />
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions>
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
import {APIService} from '@/core/util/services/api.service';
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import LeaveComment from '@/orangehrmLeavePlugin/components/LeaveComment';

export default {
  name: 'LeaveCommentModal',
  components: {
    'oxd-dialog': Dialog,
    'leave-comment': LeaveComment,
  },
  props: {
    id: {
      type: Number,
      required: false,
      default: null,
    },
    leaveRequest: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['close'],
  setup(props) {
    const apiPath = props.leaveRequest ? 'leave-requests' : 'leaves';
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/leave/${apiPath}/${props.id}/leave-comments`,
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
        comment: [required, shouldNotExceedCharLength(255)],
      },
      comments: [],
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll({limit: 0})
      .then(response => {
        const {data} = response.data;
        this.comments = data;
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

<style src="./leave-comment-modal.scss" lang="scss" scoped></style>
