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
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('claim.create_claim_request') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form ref="formRef" :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="request.claimEventId"
                type="select"
                :rules="rules.event"
                :options="claimEvents"
                :label="$t('claim.event')"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="request.currencyId"
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
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item>
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
          <submit-button />
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
import useForm from '@ohrm/core/util/composable/useForm';
import {navigate} from '@ohrm/core/util/helper/navigation';

const claimRequest = {
  claimEventId: null,
  currencyId: null,
  remarks: null,
};

export default {
  name: 'ClaimRequest',

  props: {
    currencies: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/claim/requests',
    );
    const {formRef, reset} = useForm();

    return {
      http,
      reset,
      formRef,
    };
  },

  data() {
    return {
      isLoading: false,
      request: {...claimRequest},
      rules: {
        event: [required],
        currency: [required],
        remarks: [shouldNotExceedCharLength(1000)],
      },
      claimEvents: [],
    };
  },

  computed: {
    request2() {
      if (this.request.claimEventId && this.request.currencyId) {
        return {
          claimEventId: this.request.claimEventId.id,
          currencyId: this.request.currencyId.id,
          remarks: this.request.remarks,
        };
      }
      return null;
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url: 'api/v2/claim/events',
      })
      .then((response) => {
        const {data} = response.data;
        this.claimEvents = data.map((item) => {
          return {
            id: item.id,
            label: item.name,
          };
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/claim/submitClaim');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.request2,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },
};
</script>
