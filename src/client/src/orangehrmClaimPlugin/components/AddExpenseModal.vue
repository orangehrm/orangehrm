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
  <oxd-dialog @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('claim.add_expense') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <claim-expense-type-dropdown
              v-model="expense.type"
              :label="$t('claim.expense_type')"
              :rules="rules.type"
            ></claim-expense-type-dropdown>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              v-model="expense.date"
              :label="$t('general.date')"
              :rules="rules.date"
              :years="yearsArray"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="expense.amount"
              :label="$t('general.amount')"
              :rules="rules.amount"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="expense.note"
              type="textarea"
              :label="$t('general.note')"
              :rules="rules.note"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
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
  </oxd-dialog>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {required, validDateFormat} from '@/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {yearRange} from '@ohrm/core/util/helper/year-range';
import {OxdDialog} from '@ohrm/oxd';
import {
  shouldNotExceedCharLength,
  maxCurrency,
  digitsOnlyWithTwoDecimalPoints,
} from '@ohrm/core/util/validation/rules';
import ClaimExpenseTypeDropdown from './ClaimExpenseTypeDropdown.vue';

const expenseModel = {
  type: null,
  date: null,
  amount: null,
  note: null,
};

export default {
  name: 'SaveExpense',

  components: {
    'oxd-dialog': OxdDialog,
    'claim-expense-type-dropdown': ClaimExpenseTypeDropdown,
  },

  props: {
    requestId: {
      type: Number,
      required: true,
    },
  },

  emits: ['close'],

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/claim/requests/${props.requestId}/expenses`,
    );

    const {userDateFormat} = useDateFormat();

    return {
      http,
      userDateFormat,
    };
  },

  data() {
    return {
      isLoading: false,
      yearsArray: [...yearRange()],
      expense: {
        ...expenseModel,
      },

      rules: {
        type: [required],
        date: [required, validDateFormat(this.userDateFormat)],
        note: [shouldNotExceedCharLength(1000)],
        amount: [
          required,
          maxCurrency(10000000000),
          digitsOnlyWithTwoDecimalPoints,
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          expenseTypeId: this.expense.type.id,
          date: this.expense.date,
          amount: Number(this.expense.amount).toFixed(2),
          note: this.expense.note,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.expense = {...expenseModel};
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
