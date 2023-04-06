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
  <div
    :class="{
      'orangehrm-action-button-container': allowedActions.length < 2,
      'orangehrm-action-buttons-container': allowedActions.length > 1,
    }"
  >
    <oxd-button
      v-if="isBackAllowed"
      display-type="ghost"
      class="orangehrm-sm-button"
      :label="$t('general.back')"
      @click="onBack"
    />
    <oxd-button
      v-if="isCancelAllowed"
      display-type="danger"
      class="orangehrm-sm-button"
      :label="$t('general.cancel')"
      @click="onCancel"
    />
    <oxd-button
      v-if="isRejectAllowed"
      display-type="danger"
      class="orangehrm-sm-button"
      :label="$t('general.reject')"
      @click="onReject"
    />
    <oxd-button
      v-if="isApproveAllowed"
      display-type="secondary"
      class="orangehrm-sm-button"
      :label="$t('general.approve')"
      @click="onApprove"
    />
    <oxd-button
      v-if="isPayAllowed"
      display-type="secondary"
      class="orangehrm-sm-button"
      :label="$t('claim.pay')"
      @click="onPay"
    />
    <oxd-button
      v-if="isSubmitAllowed"
      display-type="secondary"
      class="orangehrm-sm-button"
      :label="$t('general.submit')"
      @click="onSubmit"
    />
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
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
  computed: {
    isBackAllowed() {
      return true;
    },
    isCancelAllowed() {
      return this.allowedActions.includes('Cancel');
    },
    isSubmitAllowed() {
      return this.allowedActions.includes('Submit');
    },
    isApproveAllowed() {
      return this.allowedActions.includes('Approve');
    },
    isRejectAllowed() {
      return this.allowedActions.includes('Reject');
    },
    isPayAllowed() {
      return this.allowedActions.includes('Pay');
    },
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
          navigate(`/claim/submitClaim/id/${this.requestId}`);
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
          navigate(`/claim/submitClaim/id/${this.requestId}`);
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
          navigate(`/claim/submitClaim/id/${this.requestId}`);
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
          navigate(`/claim/submitClaim/id/${this.requestId}`);
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
          navigate(`/claim/submitClaim/id/${this.requestId}`);
        });
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
  @media screen and (max-width: 600px) {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
}
.orangehrm-action-button-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-end;
  padding: 25px;
}
.orangehrm-sm-button {
  margin-left: 1rem;
  @media screen and (max-width: 600px) {
    margin-bottom: 1rem;
  }
}
</style>
