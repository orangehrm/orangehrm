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
  <div class="orangehrm-action-buttons-container">
    <oxd-button
      v-if="isBackAllowed"
      display-type="ghost"
      :label="$t('general.back')"
      @click="onBack"
    />
    <oxd-button
      v-if="isCancelAllowed"
      display-type="danger"
      class="orangehrm-left-space"
      :label="$t('general.cancel')"
      @click="onCancel"
    />
    <oxd-button
      v-if="isRejectAllowed"
      display-type="danger"
      class="orangehrm-left-space"
      :label="$t('general.reject')"
      @click="onReject"
    />
    <oxd-button
      v-if="isApproveAllowed"
      display-type="secondary"
      class="orangehrm-left-space"
      :label="$t('general.approve')"
      @click="onApprove"
    />
    <oxd-button
      v-if="isPayAllowed"
      display-type="secondary"
      class="orangehrm-left-space"
      :label="$t('claim.pay')"
      @click="onPay"
    />
    <oxd-button
      v-if="isSubmitAllowed"
      display-type="secondary"
      class="orangehrm-left-space"
      :label="$t('general.submit')"
      @click="onSubmit"
    />
  </div>
</template>

<script>
import {APIService, $refs} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';

export default {
  name: 'ClaimActionButtons',
  props: {
    requestId: {
      type: Number,
      required: true,
    },
    allowedActions: {
      type: Array,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/claim/requests/${props.requestId}/action`,
    );
    return {
      http,
    };
  },
  methods: {
    onCancel() {
      this.http
        .request({
          method: 'PUT',
          data: {
            action: 'CANCEL',
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onBack();
        });
    },
    onSubmit() {
      this.http
        .request({
          method: 'PUT',
          data: {
            action: 'SUBMIT',
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onBack();
        });
    },
    onBack() {
      navigate('/claim/submitClaim');
    },
    onReject() {
      this.http
        .request({
          method: 'PUT',
          data: {
            action: 'REJECT',
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onBack();
        });
    },
    onApprove() {
      this.http
        .request({
          method: 'PUT',
          data: {
            action: 'APPROVE',
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onBack();
        });
    },
    onPay() {
      this.http
        .request({
          method: 'PUT',
          data: {
            action: 'PAY',
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onBack();
        });
    },
  },
  computed: {
    isBackAllowed() {
      console.log(this.allowedActions);
      return true;
    },
    isCancelAllowed() {
      console.log('cancel', this.allowedActions.includes('Cancel'));
      return this.allowedActions.includes('Cancel');
    },
    isSubmitAllowed() {
      console.log('submit', this.allowedActions.includes('Submit'));
      return this.allowedActions.includes('Submit');
    },
    isApproveAllowed() {
      console.log('approve', this.allowedActions.includes('Approve'));
      return this.allowedActions.includes('Approve');
    },
    isRejectAllowed() {
      console.log('reject', this.allowedActions.includes('Reject'));
      return this.allowedActions.includes('Reject');
    },
    isPayAllowed() {
      console.log('pay', this.allowedActions.includes('Pay'));
      return this.allowedActions.includes('Pay');
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-action-buttons-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-end;
  padding: 25px;
}
</style>
