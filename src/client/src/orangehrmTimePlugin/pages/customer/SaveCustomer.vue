<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.add_customer') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="customer.name"
            type="select"
            :options="subunitOptions"
            label="Name"
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
import {promiseDebounce} from '@ohrm/oxd';

const customerModel = {
  id: '',
  name: null,
  description: '',
};

export default {
  setup() {
    // API for saving customer
    const customerHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/customers',
    );

    // API for dropdown (subunits)
    const subunitHttp = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/subunits',
    );

    customerHttp.setIgnorePath('/api/v2/time/validation/customer-name');

    return {
      customerHttp,
      subunitHttp,
    };
  },

  data() {
    return {
      isLoading: false,

      customer: {...customerModel},

      subunitOptions: [{label: 'Select...', value: null}],

      rules: {
        name: [
          required,
          shouldNotExceedCharLength(50),
          // promiseDebounce(this.validateCustomerName, 500),
        ],
        description: [shouldNotExceedCharLength(255)],
      },
    };
  },

  mounted() {
    this.loadSubunits();
  },

  methods: {
    loadSubunits() {
      this.subunitHttp
        .request({
          method: 'GET',
        })
        .then((res) => {
          this.subunitOptions = [];

          res.data.data.forEach((s) => {
            this.subunitOptions.push({
              id: s.id,
              label: `${'-- '.repeat(s.level)}${
                s.unitId ? s.unitId + ' - ' : ''
              }${s.name}`,
              name: s.name,
            });
          });
        });
    },

    onSave() {
      this.isLoading = true;

      if (!this.customer.name || !this.customer.name.name) {
        this.$toast.error('Customer name is required');
        this.isLoading = false;
        return;
      }

      this.customerHttp
        .create({
          name: this.customer.name.name, // ✅ FIX
          description: this.customer.description,
        })
        .then(() => this.$toast.saveSuccess())
        .then(() => this.onCancel())
        .catch(() => {
          this.$toast.error('Failed to save customer');
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    onCancel() {
      navigate('/time/viewCustomers');
    },

    validateCustomerName(customer) {
      return new Promise((resolve) => {
        if (customer) {
          this.customerHttp
            .request({
              method: 'GET',
              url: `/api/v2/time/validation/customer-name`,
              params: {
                customerName: this.customer.name.trim(),
              },
            })
            .then((response) => {
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
