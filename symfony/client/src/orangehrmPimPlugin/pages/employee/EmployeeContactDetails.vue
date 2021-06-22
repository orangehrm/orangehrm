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
  <edit-employee-layout :employee-id="empNumber" screen="contact">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">Contact Details</oxd-text>
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
                label="Zip/Postal Code"
                v-model="contact.zipCode"
                :rules="rules.zipCode"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Country"
                v-model="contact.countryCode"
                :options="countries"
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
                label="Work"
                v-model="contact.workTelephone"
                :rules="rules.workTelephone"
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
                v-model="contact.workEmail"
                :rules="rules.workEmail"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Other Email"
                v-model="contact.otherEmail"
                :rules="rules.otherEmail"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import {
  shouldNotExceedCharLength,
  validPhoneNumberFormat,
  validEmailFormat,
} from '@orangehrm/core/util/validation/rules';

const contactDetailsModel = {
  street1: '',
  street2: '',
  city: '',
  province: '',
  countryCode: [],
  zipCode: '',
  homeTelephone: '',
  workTelephone: '',
  mobile: '',
  workEmail: '',
  otherEmail: '',
};

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    countries: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/pim/employee/${props.empNumber}/contact-details`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      contact: {...contactDetailsModel},
      rules: {
        street1: [shouldNotExceedCharLength(70)],
        street2: [shouldNotExceedCharLength(70)],
        city: [shouldNotExceedCharLength(70)],
        province: [shouldNotExceedCharLength(70)],
        zipCode: [shouldNotExceedCharLength(10)],
        homeTelephone: [shouldNotExceedCharLength(25), validPhoneNumberFormat],
        mobile: [shouldNotExceedCharLength(25), validPhoneNumberFormat],
        workTelephone: [shouldNotExceedCharLength(25), validPhoneNumberFormat],
        workEmail: [shouldNotExceedCharLength(50), validEmailFormat],
        otherEmail: [shouldNotExceedCharLength(50), validEmailFormat],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            ...this.contact,
            countryCode: this.contact.countryCode.map(item => item.id)[0],
          },
        })
        .then(response => {
          this.updateModel(response);
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateModel(response) {
      const {data} = response.data;
      this.contact = {...contactDetailsModel, ...data};
      this.contact.countryCode = this.countries.filter(
        item => item.id === data.countryCode,
      );
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        this.updateModel(response);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
