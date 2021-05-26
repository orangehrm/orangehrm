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
  <edit-employee-layout :employee-id="employeeId">
    <oxd-text tag="h6">Contact Details</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-text class="orangehrm-sub-title" tag="h6">Address</oxd-text>
      <oxd-divider />
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Street 1"
              v-model="contact.street1"
              :rules="rules.street1"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Street 2"
              v-model="contact.street2"
              :rules="rules.street2"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="City"
              v-model="contact.city"
              :rules="rules.city"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="State/Province"
              v-model="contact.province"
              :rules="rules.province"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="dropdown"
              label="Country"
              v-model="contact.country"
              :options="countries"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Zip/Postal Code"
              v-model="contact.zipCode"
              :rules="rules.zipCode"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-text class="orangehrm-sub-title" tag="h6">Telephone</oxd-text>
      <oxd-divider />
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Home"
              v-model="contact.phoneHome"
              :rules="rules.phoneHome"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Mobile"
              v-model="contact.phoneMobile"
              :rules="rules.phoneMobile"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Work"
              v-model="contact.phoneWork"
              :rules="rules.phoneWork"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-text class="orangehrm-sub-title" tag="h6">Email</oxd-text>
      <oxd-divider />
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Work Email"
              v-model="contact.emailWork"
              :rules="rules.emailWork"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Other Email"
              v-model="contact.emailOther"
              :rules="rules.emailOther"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
      <oxd-form-actions>
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </edit-employee-layout>
</template>

<script>
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';

const contactDetailsModel = {
  street1: '',
  street2: '',
  city: '',
  province: '',
  country: [],
  zipCode: '',
  phoneHome: '',
  phoneMobile: '',
  phoneWork: '',
  emailWork: '',
  emailOther: '',
};

export default {
  props: {
    employeeId: {
      type: String,
      required: true,
    },
    countries: {
      type: Array,
      default: () => [],
    },
  },
  components: {
    'edit-employee-layout': EditEmployeeLayout,
  },
  data() {
    return {
      isLoading: false,
      contact: {...contactDetailsModel},
      rules: {
        street1: [
          v => {
            return !v || v?.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        street2: [
          v => {
            return !v || v?.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        city: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        province: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        zipCode: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        phoneHome: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        phoneMobile: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        phoneWork: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        emailWork: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v ||
              v.match(
                /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9]+)+$/,
              )
              ? true
              : false || 'Expected format: admin@example.com';
          },
        ],
        emailOther: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v ||
              v.match(
                /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9]+)+$/,
              )
              ? true
              : false || 'Expected format: admin@example.com';
          },
        ],
      },
    };
  },
};
</script>
