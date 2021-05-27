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
    <oxd-text tag="h6">Edit Emergency Contact</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Name"
              v-model="contact.name"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Relationship"
              v-model="contact.relationship"
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
              label="Home Telephone"
              v-model="contact.homeTelephone"
              :rules="rules.homeTelephone"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Mobile"
              v-model="contact.mobile"
              :rules="rules.mobile"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Work Telephone"
              v-model="contact.workTelephone"
              :rules="rules.workTelephone"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-actions>
        <oxd-button
          type="button"
          displayType="ghost"
          label="Cancel"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </div>
  <oxd-divider />
</template>

<script>
import {APIService} from '@/core/util/services/api.service';

const emergencyContactModel = {
  name: '',
  relationship: '',
  homeTelephone: '',
  workTelephone: '',
  mobile: '',
};

export default {
  name: 'edit-emergency-contact',

  emits: ['close'],

  props: {
    data: {
      type: Object,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/job-titles',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      contact: {...emergencyContactModel},
      rules: {
        name: [
          v => {
            return (!!v && v.trim() !== '') || 'Required';
          },
          v => {
            return !v || v?.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        relationship: [
          v => {
            return (!!v && v.trim() !== '') || 'Required';
          },
          v => {
            return !v || v?.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        homeTelephone: [
          v => {
            return (
              v.trim() !== '' ||
              this.contact.mobile.trim() !== '' ||
              this.contact.workTelephone.trim() !== '' ||
              'At least one phone number is required'
            );
          },
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        mobile: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        workTelephone: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
      },
    };
  },

  methods: {
    // TODO: API Call
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.contact,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Added',
          });
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
