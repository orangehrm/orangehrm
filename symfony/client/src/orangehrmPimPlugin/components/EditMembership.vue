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
      Edit Membership
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Membership"
              v-model="membership.membership"
              :options="memberships"
              :rules="rules.membership"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Subscription Paid By"
              v-model="membership.subscriptionPaidBy"
              :options="paidBy"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Subscription Amount"
              v-model="membership.subscriptionFee"
              :rules="rules.subscriptionFee"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Currency"
              v-model="membership.currencyType"
              :options="currencies"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="Subscription Commence Date"
              v-model="membership.subscriptionCommenceDate"
              type="date"
              placeholder="yyyy-mm-dd"
              :rules="rules.subscriptionCommenceDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="Subscription Renewal Date"
              v-model="membership.subscriptionRenewalDate"
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
          displayType="ghost"
          label="Cancel"
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
} from '@ohrm/core/util/validation/rules';
import {yearRange} from '@ohrm/core/util/helper/year-range';

const membershipModel = {
  membership: [],
  subscriptionFee: '',
  subscriptionPaidBy: null,
  currencyType: [],
  subscriptionCommenceDate: '',
  subscriptionRenewalDate: '',
};

export default {
  name: 'edit-membership',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
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
            'Renewal date should be after the commencing date',
          ),
        ],
        subscriptionFee: [
          v => {
            return v.match(/^\d*\.?\d*$/) !== null || 'Should be a number';
          },
          v => {
            return v < 1000000000 || 'Should be less than 1000,000,000';
          },
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          subscriptionFee: this.membership.subscriptionFee,
          subscriptionCommenceDate: this.membership.subscriptionCommenceDate,
          subscriptionRenewalDate: this.membership.subscriptionRenewalDate,
          membershipId: this.membership.membership.id,
          subscriptionPaidBy: this.membership.subscriptionPaidBy?.id,
          currencyTypeId: this.membership.currencyType?.id,
        })
        .then(() => {
          return this.$toast.updateSuccess();
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

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.membership.subscriptionFee = data.subscriptionFee;
        this.membership.subscriptionCommenceDate =
          data.subscriptionCommenceDate;
        this.membership.subscriptionRenewalDate = data.subscriptionRenewalDate;
        this.membership.membership = this.memberships.find(
          item => item.id === data.membership.id,
        );
        this.membership.subscriptionPaidBy = this.paidBy.find(
          item => item.id === data.subscriptionPaidBy,
        );
        this.membership.currencyType = this.currencies.find(
          item => item.id === data.currencyType?.id,
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
