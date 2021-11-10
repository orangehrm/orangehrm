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
      Add Salary Component
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Salary Component"
              v-model="salaryComponent.name"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Pay Grade"
              v-model="salaryComponent.payGradeId"
              :options="paygrades"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Pay Frequency"
              v-model="salaryComponent.payFrequencyId"
              :options="payFrequencies"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Currency"
              v-model="salaryComponent.currencyId"
              :options="currenciesOpts"
              :rules="rules.currencyId"
              :key="currenciesOpts"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Amount"
              v-model="salaryComponent.salaryAmount"
              :rules="rules.salaryAmount"
              required
            />
            <oxd-text
              v-if="currencyInfo.length !== 0"
              class="orangehrm-input-hint"
              tag="p"
            >
              Min: {{ minAmount }} - Max: {{ maxAmount }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="--span-column-2">
            <oxd-input-field
              type="textarea"
              label="Comments"
              v-model="salaryComponent.comment"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row class="directdeposit-form-header">
        <oxd-text class="directdeposit-form-header-text" tag="p">
          Include Direct Deposit Details
        </oxd-text>
        <oxd-switch-input v-model="includeDirectDeposit" />
      </oxd-form-row>

      <oxd-form-row v-if="includeDirectDeposit">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Account Number"
              v-model="directDeposit.directDepositAccount"
              :rules="rules.directDepositAccount"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Account Type"
              v-model="directDeposit.directDepositAccountType"
              :rules="rules.directDepositAccountType"
              :options="accountTypes"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item v-if="showOptionalAccountType">
            <oxd-input-field
              label="Please Specify"
              v-model="accountType"
              :rules="rules.accountType"
              required
            />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Routing Number"
              v-model="directDeposit.directDepositRoutingNumber"
              :rules="rules.directDepositRoutingNumber"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Amount"
              v-model="directDeposit.directDepositAmount"
              :rules="rules.directDepositAmount"
              required
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
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import {
  required,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';

const salComponentModel = {
  name: '',
  salaryAmount: '',
  comment: '',
  payGradeId: null,
  payFrequencyId: null,
  currencyId: null,
};

const directDepositModel = {
  directDepositAccount: '',
  directDepositAccountType: {id: 'SAVINGS', label: 'Savings'},
  directDepositRoutingNumber: '',
  directDepositAmount: '',
};

export default {
  name: 'save-salary-component',

  components: {
    'oxd-switch-input': SwitchInput,
  },

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    paygrades: {
      type: Array,
      default: () => [],
    },
    payFrequencies: {
      type: Array,
      default: () => [],
    },
    currencies: {
      type: Array,
      default: () => [],
    },
    accountTypes: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      isLoading: false,
      includeDirectDeposit: false,
      salaryComponent: {...salComponentModel},
      directDeposit: {...directDepositModel},
      accountType: '',
      usableCurrencies: [],
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        salaryAmount: [
          required,
          v => {
            return v.match(/^\d*\.?\d*$/) !== null || 'Should be a number';
          },
          v => {
            return v < 1000000000 || 'Should be less than 1000,000,000';
          },
        ],
        comment: [shouldNotExceedCharLength(250)],
        currencyId: [required],
        directDepositAccount: [required, shouldNotExceedCharLength(100)],
        directDepositAccountType: [required],
        accountType: [required, shouldNotExceedCharLength(20)],
        directDepositRoutingNumber: [
          required,
          shouldNotExceedCharLength(9),
          v => {
            return v.match(/^\d*\.?\d*$/) !== null || 'Should be a number';
          },
        ],
        directDepositAmount: [
          required,
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
      const accountType = this.showOptionalAccountType
        ? this.accountType
        : this.directDeposit.directDepositAccountType?.id;
      this.http
        .create({
          // Paygrade fields
          salaryComponent: this.salaryComponent.name,
          salaryAmount: this.salaryComponent.salaryAmount,
          payGradeId: this.salaryComponent.payGradeId?.id,
          currencyId: this.salaryComponent.currencyId?.id,
          payFrequencyId: this.salaryComponent.payFrequencyId?.id,
          comment: this.salaryComponent.comment
            ? this.salaryComponent.comment
            : null,
          addDirectDeposit: this.includeDirectDeposit,
          // Directdeposi fields
          directDepositAccount: this.includeDirectDeposit
            ? this.directDeposit.directDepositAccount
            : undefined,
          directDepositAccountType: this.includeDirectDeposit
            ? accountType
            : undefined,
          directDepositAmount: this.includeDirectDeposit
            ? this.directDeposit.directDepositAmount
            : undefined,
          directDepositRoutingNumber: this.includeDirectDeposit
            ? this.directDeposit.directDepositRoutingNumber
            : undefined,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },

  watch: {
    'salaryComponent.payGradeId': function(newVal) {
      if (newVal?.id) {
        this.isLoading = true;
        this.http
          .request({
            url: `/api/v2/admin/pay-grades/${newVal.id}/currencies`,
            method: 'GET',
          })
          .then(response => {
            const {data} = response.data;
            this.usableCurrencies = data.map(item => {
              return {
                id: item.currencyType.id,
                name: item.currencyType.name,
                minAmount: item.minSalary,
                maxAmount: item.maxSalary,
              };
            });
            const currency = this.salaryComponent.currencyId;
            const currencyIndex = this.usableCurrencies.findIndex(
              item => item.id === currency?.id,
            );
            this.salaryComponent.currencyId =
              currencyIndex === -1 ? null : this.salaryComponent.currencyId;
          })
          .then(() => {
            this.isLoading = false;
          });
      } else {
        this.usableCurrencies = [];
      }
    },
  },

  computed: {
    showOptionalAccountType() {
      return this.directDeposit.directDepositAccountType?.id == 'OTHER';
    },
    minAmount() {
      return this.currencyInfo.length === 0
        ? 0
        : this.currencyInfo[0].minAmount;
    },
    maxAmount() {
      return this.currencyInfo.length === 0
        ? 999999999
        : this.currencyInfo[0].maxAmount;
    },
    currenciesOpts() {
      const paygrade = this.salaryComponent.payGradeId?.id;
      if (!paygrade) {
        return this.currencies;
      } else if (paygrade && this.usableCurrencies.length > 0) {
        return this.currencies.filter(
          item =>
            this.usableCurrencies.findIndex(
              currency => currency.id === item.id,
            ) > -1,
        );
      } else {
        return [];
      }
    },
    currencyInfo() {
      return this.usableCurrencies.filter(
        item => item.id === this.salaryComponent.currencyId?.id,
      );
    },
  },

  mounted() {
    this.$nextTick(() => {
      this.rules.salaryAmount.push(v => {
        return v >= this.minAmount || 'Should be within Min/Max values';
      });
      this.rules.salaryAmount.push(v => {
        return v <= this.maxAmount || 'Should be within Min/Max values';
      });
    });
  },
};
</script>

<style lang="scss" scoped>
.directdeposit-form-header {
  display: flex;
  padding: 1rem;
  &-text {
    font-size: 0.8rem;
    margin-right: 1rem;
  }
}
</style>
