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
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('pim.edit_emergency_contact') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="contact.name"
              :label="$t('general.name')"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="contact.relationship"
              :label="$t('pim.relationship')"
              :rules="rules.relationship"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model.trim="contact.homePhone"
              :label="$t('pim.home_telephone')"
              :rules="rules.homePhone"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model.trim="contact.mobilePhone"
              :label="$t('general.mobile')"
              :rules="rules.mobilePhone"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model.trim="contact.officePhone"
              :label="$t('pim.work_telephone')"
              :rules="rules.officePhone"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

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
  <oxd-divider />
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
  validPhoneNumberFormat,
} from '@ohrm/core/util/validation/rules';

const emergencyContactModel = {
  name: '',
  relationship: '',
  homePhone: '',
  officePhone: '',
  mobilePhone: '',
};

export default {
  name: 'EditEmergencyContact',

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

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      contact: {...emergencyContactModel},
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        relationship: [required, shouldNotExceedCharLength(100)],
        homePhone: [
          validPhoneNumberFormat,
          shouldNotExceedCharLength(30),
          v => {
            return (
              v !== '' ||
              this.contact.mobilePhone !== '' ||
              this.contact.officePhone !== '' ||
              this.$t('pim.at_least_one_phone_number_is_required')
            );
          },
        ],
        mobilePhone: [validPhoneNumberFormat, shouldNotExceedCharLength(30)],
        officePhone: [validPhoneNumberFormat, shouldNotExceedCharLength(30)],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.contact = {...emergencyContactModel, ...data};
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
          ...this.contact,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.contact = {...emergencyContactModel};
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
