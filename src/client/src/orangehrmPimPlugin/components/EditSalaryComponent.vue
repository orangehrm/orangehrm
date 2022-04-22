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
      {{ $t('pim.edit_salary_component') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.name"
              :label="$t('pim.salary_component')"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.payGradeId"
              type="select"
              :label="$t('general.pay_grade')"
              :options="paygrades"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.payFrequencyId"
              type="select"
              :label="$t('pim.pay_frequency')"
              :options="payFrequencies"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              :key="currenciesOpts"
              v-model="salaryComponent.currencyId"
              type="select"
              :label="$t('general.currency')"
              :options="currenciesOpts"
              :rules="rules.currencyId"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.salaryAmount"
              :label="$t('pim.amount')"
              :rules="rules.salaryAmount"
              required
            />
            <oxd-text
              v-if="minAmount !== undefined || maxAmount !== undefined"
              class="orangehrm-input-hint"
              tag="p"
            >
              Min: {{ minAmount ?? 0 }} - Max: {{ maxAmount ?? 0 }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="--span-column-2">
            <oxd-input-field
              v-model="salaryComponent.comment"
              type="textarea"
              :label="$t('general.comments')"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row class="directdeposit-form-header">
        <oxd-text class="directdeposit-form-header-text" tag="p">
          {{ $t('pim.include_direct_deposit_details') }}
        </oxd-text>
        <oxd-switch-input v-model="includeDirectDeposit" />
      </oxd-form-row>

      <oxd-form-row v-if="includeDirectDeposit">
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="directDeposit.directDepositAccount"
              :label="$t('pim.account_number')"
              :rules="rules.directDepositAccount"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="directDeposit.directDepositAccountType"
              type="select"
              :label="$t('pim.account_type')"
              :rules="rules.directDepositAccountType"
              :options="accountTypes"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item v-if="showOptionalAccountType">
            <oxd-input-field
              v-model="accountType"
              :label="$t('pim.please_specify')"
              :rules="rules.accountType"
              required
            />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="directDeposit.directDepositRoutingNumber"
              :label="$t('pim.routing_number')"
              :rules="rules.directDepositRoutingNumber"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="directDeposit.directDepositAmount"
              :label="$t('pim.amount')"
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
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import {
  digitsOnlyWithDecimalPoint,
  maxCurrency,
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

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
  directDepositAccountType: null,
  directDepositRoutingNumber: '',
  directDepositAmount: '',
};

export default {
  name: 'EditSalaryComponent',

  components: {
    'oxd-switch-input': SwitchInput,
  },

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
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

  emits: ['close'],

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
          digitsOnlyWithDecimalPoint,
          maxCurrency(1000000000),
        ],
        comment: [shouldNotExceedCharLength(250)],
        currencyId: [required],
        directDepositAccount: [required, shouldNotExceedCharLength(100)],
        directDepositAccountType: [required],
        accountType: [required, shouldNotExceedCharLength(20)],
        directDepositRoutingNumber: [
          required,
          shouldNotExceedCharLength(9),
          digitsOnlyWithDecimalPoint,
        ],
        directDepositAmount: [
          required,
          digitsOnlyWithDecimalPoint,
          maxCurrency(1000000000),
        ],
      },
    };
  },

  computed: {
    showOptionalAccountType() {
      return this.directDeposit.directDepositAccountType?.id == 'OTHER';
    },
    minAmount() {
      return this.currencyInfo?.minAmount;
    },
    maxAmount() {
      return this.currencyInfo?.maxAmount;
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
      return this.usableCurrencies.find(
        item => item.id === this.salaryComponent.currencyId?.id,
      );
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
              currencyIndex === -1 ? [] : this.salaryComponent.currencyId;
          })
          .then(() => {
            this.isLoading = false;
          });
      } else {
        this.usableCurrencies = [];
      }
    },
  },

  mounted() {
    this.$nextTick(() => {
      this.rules.salaryAmount.push(v => {
        const min = this.minAmount ? this.minAmount : 0;
        return v >= min || this.$t('pim.should_be_within_min_max_values');
      });
      this.rules.salaryAmount.push(v => {
        const max = this.maxAmount ? this.maxAmount : 999999999;
        return v <= max || this.$t('pim.should_be_within_min_max_values');
      });
    });
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.salaryComponent.name = data.salaryName;
        this.salaryComponent.salaryAmount = data.amount;
        this.salaryComponent.comment = data.comment ? data.comment : '';
        this.salaryComponent.payGradeId = this.paygrades.find(
          item => item.id === data.payGrade?.id,
        );
        this.salaryComponent.payFrequencyId = this.payFrequencies.find(
          item => item.id === data.payPeriod?.id,
        );
        this.salaryComponent.currencyId = this.currencies.find(
          item => item.id === data.currencyType?.id,
        );
        if (data.directDebit.id !== null) {
          this.includeDirectDeposit = true;
          this.directDeposit.directDepositAccount = data.directDebit.account;
          const accountType = this.accountTypes.find(
            item => item.id === data.directDebit.accountType,
          );
          this.directDeposit.directDepositAccountType = accountType
            ? accountType
            : {id: 'OTHER', label: this.$t('pim.other')};
          this.accountType =
            accountType.length === 0 ? data.directDebit.accountType : '';
          this.directDeposit.directDepositRoutingNumber =
            data.directDebit.routingNumber;
          this.directDeposit.directDepositAmount = data.directDebit.amount;
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      const accountType = this.showOptionalAccountType
        ? this.accountType
        : this.directDeposit.directDepositAccountType?.id;
      this.http
        .update(this.data.id, {
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
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
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
