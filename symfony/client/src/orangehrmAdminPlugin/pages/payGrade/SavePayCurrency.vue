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
  <div class="orangehrm-card-container">
    <oxd-text tag="h6" class="orangehrm-main-title">{{
      $t('admin.add_currency')
    }}</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="payCurrency.currencyId"
              type="select"
              :label="$t('general.currency')"
              :options="currencies"
              :rules="rules.currencyId"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="payCurrency.minSalary"
              :label="$t('admin.minimum_salary')"
              :rules="rules.minSalary"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="payCurrency.maxSalary"
              :label="$t('admin.maximum_salary')"
              :rules="rules.maxSalary"
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
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  digitsOnly,
  maxCurrency,
} from '@ohrm/core/util/validation/rules';
import {minValueShouldBeLowerThanMaxValue} from '@/core/util/validation/rules';
const payCurrencyModel = {
  currencyId: null,
  minSalary: '',
  maxSalary: '',
};
export default {
  name: 'SavePayCurrency',
  props: {
    payGradeId: {
      type: String,
      required: true,
    },
  },

  emits: ['close'],
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/admin/pay-grades/${props.payGradeId}/currencies`,
    );
    return {http};
  },

  data() {
    return {
      isLoading: false,
      payCurrency: {...payCurrencyModel},
      currencies: [],
      rules: {
        currencyId: [required],
        minSalary: [maxCurrency(1000000000), digitsOnly],
        maxSalary: [
          maxCurrency(1000000000),
          digitsOnly,
          minValueShouldBeLowerThanMaxValue(
            () => this.payCurrency.minSalary,
            this.$t('admin.should_be_higher_than_minimum_salary'),
          ),
        ],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url: `api/v2/admin/pay-grades/${this.payGradeId}/currencies/allowed?limit=0`,
        params: {
          limit: 0,
        },
      })
      .then(response => {
        const {data} = response.data;
        this.currencies = data.map(item => {
          return {
            id: item.id,
            label: item.id + ' - ' + item.name,
          };
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.payCurrency,
          currencyId: this.payCurrency.currencyId.id,
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
};
</script>
