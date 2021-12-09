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
    <oxd-text tag="h6" class="orangehrm-main-title">Edit Currency</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="payCurrency.name"
              label="Currency"
              required
              readonly
              disabled
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="payCurrency.minSalary"
              label="Minimum Salary"
              :rules="rules.minSalary"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="payCurrency.maxSalary"
              label="Maximum Salary"
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
          label="Cancel"
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
  maxCurrency,
  digitsOnly,
  minValueShouldBeLowerThanMaxValue,
} from '@ohrm/core/util/validation/rules';
const payCurrencyModel = {
  currencyId: null,
  minSalary: '',
  maxSalary: '',
};
export default {
  name: 'EditPayCurrency',
  props: {
    payGradeId: {
      type: String,
      required: true,
    },
    data: {
      type: Object,
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
      rules: {
        currencyId: [required],
        minSalary: [maxCurrency(1000000000), digitsOnly],
        maxSalary: [
          maxCurrency(1000000000),
          digitsOnly,
          minValueShouldBeLowerThanMaxValue(
            () => this.payCurrency.minSalary,
            'Should be higher than Minimum Salary',
          ),
        ],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.payCurrency.name = data.currencyType.name;
        this.payCurrency.minSalary = data.minSalary ? data.minSalary : '0';
        this.payCurrency.maxSalary = data.maxSalary ? data.maxSalary : '0';
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          minSalary: this.payCurrency.minSalary,
          maxSalary: this.payCurrency.maxSalary,
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
