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
  <div class="orangehrm-horizontal-padding orangehrm-top-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">Edit License</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="License Type"
              v-model="license.name"
              required
              readonly
              disabled
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="License Number"
              v-model="license.licenseNo"
              :rules="rules.licenseNo"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Issued Date"
              v-model="license.issuedDate"
              :rules="rules.issuedDate"
              type="date"
              placeholder="yyyy-mm-dd"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Expiry Date"
              v-model="license.expiryDate"
              :rules="rules.expiryDate"
              type="date"
              placeholder="yyyy-mm-dd"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

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
    <oxd-divider />
  </div>
</template>

<script>
import {
  validDateFormat,
  shouldNotExceedCharLength,
  endDateShouldBeAfterStartDate,
} from '@orangehrm/core/util/validation/rules';

const licenseModel = {
  name: '',
  licenseNo: '',
  issuedDate: '',
  expiryDate: '',
};

export default {
  name: 'edit-license',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      license: {...licenseModel},
      rules: {
        licenseNo: [shouldNotExceedCharLength(50)],
        issuedDate: [validDateFormat()],
        expiryDate: [
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.license.issuedDate,
            'Expiry date should be after issued date',
          ),
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          licenseNo: this.license.licenseNo,
          issuedDate: this.license.issuedDate,
          expiryDate: this.license.expiryDate,
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

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.license.name = data.license.name;
        this.license.licenseNo = data.licenseNo ? data.licenseNo : '';
        this.license.issuedDate = data.issuedDate;
        this.license.expiryDate = data.expiryDate;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
