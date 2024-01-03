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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('claim.assign_claim') }}
        </oxd-text>
        <oxd-divider />
        <oxd-form :loading="isLoading">
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  v-model="employeeName"
                  :label="$t('general.employee')"
                  disabled
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
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
                  v-model="formattedEventName"
                  :label="$t('claim.event')"
                  disabled
                />
              </oxd-grid-item>
              <oxd-grid-item>
                <oxd-input-field
                  v-model="statusMap[request.status]"
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
        :request-id="id"
        :currency="currency"
        :can-edit="canEdit"
      ></claim-expenses>
      <br />
      <claim-attachment
        :request-id="id"
        :can-edit="canEdit"
        :allowed-file-types="allowedFileTypes"
        :max-file-size="maxFileSize"
      ></claim-attachment>
      <br />
      <claim-action-buttons
        :request-id="id"
        :allowed-actions="allowedActions"
        :is-assigned="true"
      />
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import ClaimAttachment from '@/orangehrmClaimPlugin/components/ClaimAttachment.vue';
import ClaimExpenses from '@/orangehrmClaimPlugin/components/ClaimExpenses.vue';
import ClaimActionButtons from '@/orangehrmClaimPlugin/components/ClaimActionButtons.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'AssignClaim',

  components: {
    'claim-attachment': ClaimAttachment,
    'claim-expenses': ClaimExpenses,
    'claim-action-buttons': ClaimActionButtons,
  },

  props: {
    id: {
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
    empNumber: {
      type: Number,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/claim/employees/${props.empNumber}/requests`,
    );

    return {
      http,
    };
  },

  data() {
    const statusMap = {
      SUBMITTED: this.$t('time.submitted'),
      APPROVED: this.$t('time.approved'),
      REJECTED: this.$t('leave.rejected'),
      CANCELLED: this.$t('leave.cancelled'),
      PAID: this.$t('claim.paid'),
      INITIATED: this.$t('claim.initiated'),
    };
    return {
      isLoading: false,
      request: {},
      claimEvent: {},
      currency: {},
      response: {},
      allowedActions: [],
      employee: {},
      statusMap,
      employeeName: '',
    };
  },

  computed: {
    canEdit() {
      if (this.allowedActions) {
        return this.allowedActions.includes('Submit');
      }
      return false;
    },
    formattedEventName() {
      return this.claimEvent.isDeleted
        ? `${this.claimEvent.name} ${this.$t('general.deleted')}`
        : !this.claimEvent.status
        ? `${this.claimEvent.name} (${this.$t('performance.inactive')})`
        : this.claimEvent.name;
    },
  },

  beforeMount() {
    const {$tEmpName} = useEmployeeNameTranslate();
    this.isLoading = true;
    this.http
      .get(this.id)
      .then((res) => {
        const {data, meta} = res.data;
        this.response = res.data;
        this.request = data;
        this.claimEvent = data.claimEvent;
        this.currency = data.currencyType;
        this.allowedActions = meta.allowedActions.map((action) => action.name);
        this.employee = meta.employee;
        this.employeeName = $tEmpName(this.employee);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/claim/submitClaim');
    },
  },
};
</script>
