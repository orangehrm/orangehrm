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
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('general.add_membership') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="membership.membership"
              type="select"
              :label="$t('pim.membership')"
              :options="memberships"
              :rules="rules.membership"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="membership.subscriptionPaidBy"
              type="select"
              :label="$t('pim.subscription_paid_by')"
              :options="paidBy"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="membership.subscriptionFee"
              :label="$t('pim.subscription_amount')"
              :rules="rules.subscriptionFee"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="membership.currencyType"
              type="select"
              :label="$t('general.currency')"
              :options="currencies"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="membership.subscriptionCommenceDate"
              :label="$t('pim.subscription_commence_date')"
              type="date"
              placeholder="yyyy-mm-dd"
              :rules="rules.subscriptionCommenceDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="membership.subscriptionRenewalDate"
              :label="$t('pim.subscription_renewal_date')"
              type="date"
              :years="yearArray"
              placeholder="yyyy-mm-dd"
              :rules="rules.subscriptionRenewalDate"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </div>
  <oxd-divider />
</template>

<script>
import {
  required,
  validDateFormat,
  endDateShouldBeAfterStartDate,
  digitsOnlyWithDecimalPoint,
  maxCurrency,
} from '@ohrm/core/util/validation/rules';
import {yearRange} from '@ohrm/core/util/helper/year-range';

const membershipModel = {
  membershipId: [],
  subscriptionFee: '',
  subscriptionPaidBy: null,
  currencyTypeId: [],
  subscriptionCommenceDate: '',
  subscriptionRenewalDate: '',
};

export default {
  name: 'SaveMembership',

  props: {
    http: {
      type: Object,
      required: true,
    },
    currencies: {
      type: Array,
      default: () => [],
    },
    paidBy: {
      type: Array,
      default: () => [],
    },
    memberships: {
      type: Array,
      default: () => [],
    },
  },

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      membership: {...membershipModel},
      yearArray: [...yearRange()],
      rules: {
        membership: [required],
        subscriptionRenewalDate: [
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.membership.subscriptionCommenceDate,
            this.$t('pim.renewal_date_should_be_after_the_commencing_date'),
          ),
        ],
        subscriptionFee: [digitsOnlyWithDecimalPoint, maxCurrency(1000000000)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          subscriptionFee: this.membership.subscriptionFee,
          subscriptionCommenceDate: this.membership.subscriptionCommenceDate,
          subscriptionRenewalDate: this.membership.subscriptionRenewalDate,
          membershipId: this.membership.membership.id,
          subscriptionPaidBy: this.membership.subscriptionPaidBy?.id,
          currencyTypeId: this.membership.currencyType?.id,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.membership = {...membershipModel};
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
