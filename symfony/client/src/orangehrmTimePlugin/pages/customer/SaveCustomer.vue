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
            :label="$t('general.name')"
            v-model="customer.name"
            :rules="rules.name"
            required
          />
        </oxd-form-row>
        <oxd-form-row>
          <oxd-input-field
            type="textarea"
            :label="$t('general.description')"
            placeholder="Type description here"
            v-model="customer.description"
            :rules="rules.description"
          />
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            displayType="ghost"
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

const customerModel = {
  id: '',
  name: '',
  description: '',
};

export default {
  data() {
    return {
      isLoading: false,
      customer: {...customerModel},
      rules: {
        name: [required, shouldNotExceedCharLength(50)],
        description: [shouldNotExceedCharLength(250)],
      },
    };
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/customers',
    );
    return {
      http,
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
          this.customer.name = '';
          this.customer.description = '';
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/time/viewCustomers');
    },
  },
  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.rules.name.push(v => {
          const index = data.findIndex(item => item.name == v);
          return index === -1 || 'Already exists';
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
