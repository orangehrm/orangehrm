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
                v-model="contact.street1"
                label="Street 1"
                :rules="rules.street1"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.street2"
                label="Street 2"
                :rules="rules.street2"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.city"
                label="City"
                :rules="rules.city"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.province"
                label="State/Province"
                :rules="rules.province"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.zipCode"
                label="Zip/Postal Code"
                :rules="rules.zipCode"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.countryCode"
                type="select"
                label="Country"
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
                v-model="contact.homeTelephone"
                label="Home"
                :rules="rules.homeTelephone"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.mobile"
                label="Mobile"
                :rules="rules.mobile"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.workTelephone"
                label="Work"
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
                v-model="contact.workEmail"
                label="Work Email"
                :rules="rules.workEmail"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="contact.otherEmail"
                label="Other Email"
                :rules="rules.otherEmail"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import {
  shouldNotExceedCharLength,
  validPhoneNumberFormat,
  validEmailFormat,
} from '@ohrm/core/util/validation/rules';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';

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
        workEmail: [
          shouldNotExceedCharLength(50),
          validEmailFormat,
          promiseDebounce(this.validateWorkEmail, 500),
        ],
        otherEmail: [shouldNotExceedCharLength(50), validEmailFormat],
      },
    };
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

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            ...this.contact,
            countryCode: this.contact.countryCode?.id,
          },
        })
        .then(response => {
          this.updateModel(response);
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    validateWorkEmail(contact) {
      return new Promise(resolve => {
        if (contact) {
          this.http
            .request({
              method: 'GET',
              url: `api/v2/pim/employees/${this.empNumber}/contact-details/validation/work-emails`,
              params: {
                workEmail: this.contact.workEmail,
              },
            })
            .then(response => {
              const {data} = response.data;
              return data.valid === true
                ? resolve(true)
                : resolve('Already exist');
            });
        } else {
          resolve(true);
        }
      });
    },

    updateModel(response) {
      const {data} = response.data;
      this.contact = {...contactDetailsModel, ...data};
      this.contact.countryCode = this.countries.find(
        item => item.id === data.countryCode,
      );
    },
  },
};
</script>
