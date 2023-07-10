<template>
  <oxd-dialog
    :style="{width: '90%', maxWidth: '450px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('time.add_customer') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
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
      <oxd-form-actions class="orangehrm-form-action">
        <required-text />
        <oxd-button
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <oxd-button
          display-type="secondary"
          :label="$t('general.save')"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {OxdDialog} from '@ohrm/oxd';
import useServerValidation from '@/core/util/composable/useServerValidation';

const customerModel = {
  id: '',
  name: '',
  description: '',
};

export default {
  name: 'AddCustomerModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  emits: ['close'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/customers',
    );
    http.setIgnorePath('/api/v2/time/validation/customer-name');
    const {createUniqueValidator} = useServerValidation(http);
    const customerNameUniqueValidation = createUniqueValidator(
      'customer',
      'name',
      {
        matchByField: 'deleted',
        matchByValue: 'false',
      },
    );
    return {
      http,
      customerNameUniqueValidation,
    };
  },
  data() {
    return {
      isLoading: false,
      customer: {...customerModel},
      rules: {
        name: [
          required,
          this.customerNameUniqueValidation,
          shouldNotExceedCharLength(50),
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
        .then((response) => {
          const {data} = response.data;
          this.$toast.saveSuccess();
          this.$emit('close', data);
        });
    },
    onCancel() {
      this.$emit('close');
    },
  },
};
</script>
