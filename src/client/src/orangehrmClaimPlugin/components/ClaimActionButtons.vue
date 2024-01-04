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
  <div
    :class="{
      'orangehrm-action-button-container': allowedActions.length < 2,
      'orangehrm-action-buttons-container': allowedActions.length > 1,
    }"
  >
    <oxd-button
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
      @click="onClaimAction('CANCEL')"
    />
    <oxd-button
      v-if="isRejectAllowed"
      display-type="danger"
      class="orangehrm-sm-button"
      :label="$t('general.reject')"
      @click="onClaimAction('REJECT')"
    />
    <oxd-button
      v-if="isApproveAllowed"
      display-type="secondary"
      class="orangehrm-sm-button"
      :label="$t('general.approve')"
      @click="onClaimAction('APPROVE')"
    />
    <oxd-button
      v-if="isPayAllowed"
      display-type="secondary"
      class="orangehrm-sm-button"
      :label="$t('claim.pay')"
      @click="onClaimAction('PAY')"
    />
    <oxd-button
      v-if="isSubmitAllowed"
      display-type="secondary"
      class="orangehrm-sm-button"
      :label="$t('general.submit')"
      @click="onClaimAction('SUBMIT')"
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
    isAssigned: {
      default: false,
      type: Boolean,
      required: false,
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
    onClaimAction(action) {
      this.http
        .request({
          method: 'PUT',
          data: {
            action: action,
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.isAssigned
            ? navigate(`/claim/assignClaim/id/${this.requestId}`)
            : navigate(`/claim/submitClaim/id/${this.requestId}`);
        });
    },
    onBack() {
      this.isAssigned
        ? navigate('/claim/viewAssignClaim')
        : navigate('/claim/viewClaim');
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
    align-items: flex-end;
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
