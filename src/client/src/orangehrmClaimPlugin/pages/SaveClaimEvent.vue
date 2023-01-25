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
  <oxd-form :loading="isLoading" @submit="onSave">
    <oxd-form-row>
      <oxd-input-field v-model="claimEvent.name" label="name" required />
    </oxd-form-row>

    <oxd-form-row>
      <oxd-input-field
        v-model="claimEvent.description"
        type="textarea"
        label="description"
        placeholder="Type description here"
      />
    </oxd-form-row>

    <oxd-form-row>
      <oxd-grid-item class="orangehrm-switch-filter --span-column-2">
        <oxd-text class="orangehrm-switch-filter-text" tag="p">
          Action
        </oxd-text>
        <oxd-input-field v-model="claimEvent.status" type="switch" />
      </oxd-grid-item>
    </oxd-form-row>
  </oxd-form>
  <oxd-divider />

  <oxd-form-actions>
    <required-text />
    <oxd-button display-type="ghost" label="cancel" @click="onCancel" />
    <submit-button />
  </oxd-form-actions>
</template>

<script>
//TODO: Add validation
//TODO: Add API call
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
export default {
  name: 'SaveClaimEvent',
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/claim/events',
    );
    return {
      http,
    };
  },
  data() {
    return {
      claimEvent: {
        name: '',
        description: '',
        status: true,
      },
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        description: [shouldNotExceedCharLength(1000)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create(this.claimEvent)
        .then((res) => {
          return res.data;
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/claim/events');
    },
  },
};
</script>
