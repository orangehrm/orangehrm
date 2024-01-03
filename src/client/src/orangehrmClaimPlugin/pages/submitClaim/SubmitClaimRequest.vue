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
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('claim.create_claim_request') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <claim-event-dropdown
                v-model="request.event"
                :rules="rules.event"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="request.currency"
                type="select"
                :rules="rules.currency"
                :options="currencies"
                :label="$t('general.currency')"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="--span-column-2">
              <oxd-input-field
                v-model="request.remarks"
                type="textarea"
                :label="$t('claim.remarks')"
                :rules="rules.remarks"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button :label="$t('claim.create')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import ClaimEventDropdownVue from '../../components/ClaimEventDropdown.vue';

const claimRequest = {
  event: null,
  currency: null,
  remarks: null,
};

export default {
  name: 'SubmitClaimRequest',

  components: {
    'claim-event-dropdown': ClaimEventDropdownVue,
  },

  props: {
    currencies: {
      type: Array,
      default: () => [],
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
      request: {...claimRequest},
      id: 0,
      rules: {
        event: [required],
        currency: [required],
        remarks: [shouldNotExceedCharLength(1000)],
      },
    };
  },

  methods: {
    onCancel() {
      navigate('/claim/viewClaim');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          claimEventId: this.request.event.id,
          currencyId: this.request.currency.id,
          remarks: this.request.remarks,
        })
        .then((res) => {
          this.id = res.data.data.id;
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate('/claim/submitClaim/id/{id}', {id: this.id});
        });
    },
  },
};
</script>
