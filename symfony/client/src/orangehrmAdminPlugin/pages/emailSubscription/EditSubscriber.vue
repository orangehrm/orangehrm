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
  <oxd-dialog
    @update:show="onCancel"
    :style="{width: '90%', maxWidth: '600px'}"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">Edit Subscriber</oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field
          label="Name"
          v-model="subscriber.name"
          :rules="rules.name"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          label="Email"
          v-model="subscriber.email"
          :rules="rules.email"
          required
        />
      </oxd-form-row>
      <oxd-divider />

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
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Dialog from '@orangehrm/oxd/core/components/Dialog/Dialog';
import {
  required,
  validEmailFormat,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';

const subscriberModel = {
  name: '',
  email: '',
};

export default {
  name: 'edit-subscriber',
  props: {
    data: {
      type: Object,
    },
  },
  components: {
    'oxd-dialog': Dialog,
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/admin/email-subscriptions/${props.data.subscriptionId}/subscribers`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      subscriber: {...subscriberModel},
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        email: [required, validEmailFormat, shouldNotExceedCharLength(100)],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          ...this.subscriber,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.subscriber = {...subscriberModel};
      this.$emit('close', true);
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.subscriber.name = data.name;
        this.subscriber.email = data.email;
        // Fetch list data for unique test
        return this.http.getAll();
      })
      .then(response => {
        const {data} = response.data;
        if (data) {
          this.rules.email.push(v => {
            const index = data.findIndex(item => item.email == v);
            if (index > -1) {
              const {id} = data[index];
              return id != this.data.id ? 'Already exists' : true;
            } else {
              return true;
            }
          });
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style scoped>
.oxd-overlay {
  z-index: 1100 !important;
}
.level-label {
  font-size: 0.75rem;
}
</style>
