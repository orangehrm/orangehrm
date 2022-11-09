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
        {{ $t('time.add_customer') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="customer.name"
            :label="$t('general.name')"
            :rules="rules.name"
            required
          />
        </oxd-form-row>
        <oxd-form-row>
          <oxd-input-field
            v-model="customer.description"
            type="textarea"
            :label="$t('general.description')"
            placeholder="Type description here"
            :rules="rules.description"
          />
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
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';

const customerModel = {
  id: '',
  name: '',
  description: '',
};

export default {
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/customers',
    );
    http.setIgnorePath('api/v2/time/validation/customer-name');
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      customer: {...customerModel},
      rules: {
        name: [
          required,
          shouldNotExceedCharLength(50),
          promiseDebounce(this.validateCustomerName, 500),
        ],
        description: [shouldNotExceedCharLength(255)],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          name: this.customer.name,
          description: this.customer.description,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/time/viewCustomers');
    },
    validateCustomerName(customer) {
      return new Promise(resolve => {
        if (customer) {
          this.http
            .request({
              method: 'GET',
              url: `api/v2/time/validation/customer-name`,
              params: {
                customerName: this.customer.name.trim(),
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve(this.$t('general.already_exists'));
            });
        } else {
          resolve(true);
        }
      });
    },
  },
};
</script>
