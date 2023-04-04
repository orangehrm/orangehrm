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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('claim.submit_claim') }}
        </oxd-text>

        <oxd-divider />

        <oxd-form :loading="isLoading">
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  v-model="request.referenceId"
                  :label="$t('claim.reference_id')"
                  disabled
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="claimEvent.name"
                  :label="$t('claim.event')"
                  disabled
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="computedState"
                  :label="$t('general.status')"
                  disabled
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  v-model="currency.name"
                  :label="$t('general.currency')"
                  disabled
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  v-model="request.remarks"
                  :label="$t('claim.remarks')"
                  type="textarea"
                  disabled
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
        </oxd-form>
      </div>
      <claim-expenses
        :requestId="id"
        :currency="currency"
        :can-edit="canEdit"
      ></claim-expenses>
      <br />
      <claim-attachment :requestId="id" :can-edit="canEdit"></claim-attachment>
      <br />
      <claim-action-buttons :requestId="id" :allowedActions="allowedActions" />
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import ClaimAttachment from '../../components/ClaimAttachment.vue';
import ClaimExpenses from '../../components/ClaimExpenses.vue';
import ClaimActionButtons from '@/orangehrmClaimPlugin/components/ClaimActionButtons.vue';

export default {
  name: 'SubmitClaim',

  components: {
    ClaimAttachment,
    ClaimExpenses,
    ClaimActionButtons,
  },

  props: {
    id: {
      type: Number,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/claim/requests',
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      request: {},
      claimEvent: {},
      currency: {},
      response: {},
      allowedActions: [],
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.id)
      .then((res) => {
        const {data} = res.data;
        const {meta} = res.data;
        this.response = res.data;
        this.request = data;
        //this.claimEvent = res.data.data.claimEvent;
        this.claimEvent = data.claimEvent;
        //this.currency = res.data.data.currencyType;
        this.currency = data.currencyType;
        //const aa = this.response.meta.allowedActions;
        this.allowedActions = meta.allowedActions.map((action) => action.name);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  computed: {
    canEdit() {
      if (this.allowedActions) {
        return this.allowedActions.includes('Submit');
      }
      return false;
    },
    computedState() {
      const statusMap = {
        SUBMITTED: 'Submitted',
        APPROVED: 'Approved',
        REJECTED: 'Rejected',
        CANCELLED: 'Cancelled',
        PAID: 'Paid',
        INITIATED: 'Initiated',
      };
      return statusMap[this.request.status];
    },
  },

  methods: {
    onCancel() {
      navigate('/claim/submitClaim');
    },
  },
};
</script>

<style src="./submitClaim.scss" lang="scss" scoped></style>