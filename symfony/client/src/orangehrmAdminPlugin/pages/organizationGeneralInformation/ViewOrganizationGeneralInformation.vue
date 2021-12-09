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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title"
          >General Information</oxd-text
        >
        <oxd-switch-input
          v-model="editable"
          option-label="Edit"
          label-position="left"
        />
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <oxd-input-field
                v-model="organization.name"
                label="Organization Name"
                :rules="rules.name"
                :disabled="!editable"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-group label="Number of Employees">
                <oxd-text tag="p" class="no-of-employees-value">
                  {{ organization.noOfEmployees }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.registrationNumber"
                label="Registration Number"
                :rules="rules.registrationNumber"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.taxId"
                label="Tax ID"
                :rules="rules.taxId"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.phone"
                label="Phone"
                :rules="rules.phone"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.fax"
                label="Fax"
                :rules="rules.fax"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.email"
                label="Email"
                :rules="rules.email"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.street1"
                label="Address Street 1"
                :rules="rules.street1"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.street2"
                label="Address Street 2"
                :rules="rules.street2"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.city"
                label="City"
                :rules="rules.city"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.province"
                label="State/Province"
                :rules="rules.province"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.zipCode"
                label="Zip/Postal Code"
                :rules="rules.zipCode"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="organization.country"
                label="Country"
                type="select"
                :rules="rules.country"
                :options="countryList"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="organization.note"
              label="Note"
              type="textarea"
              :rules="rules.note"
              :disabled="!editable"
            />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <submit-button v-if="editable" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import {
  required,
  shouldNotExceedCharLength,
  validEmailFormat,
  validPhoneNumberFormat,
} from '@ohrm/core/util/validation/rules';

export default {
  components: {
    'oxd-switch-input': SwitchInput,
  },
  props: {
    numberOfEmployees: {
      type: Number,
      required: true,
    },
    countryList: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/organization',
    );
    return {
      http,
    };
  },

  data() {
    return {
      editable: false,
      isLoading: false,
      organization: {
        name: '',
        noOfEmployees: this.numberOfEmployees,
        registrationNumber: '',
        taxId: '',
        phone: '',
        fax: '',
        email: '',
        street1: '',
        street2: '',
        city: '',
        province: '',
        country: '',
        zipCode: '',
        note: '',
      },
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        registrationNumber: [shouldNotExceedCharLength(30)],
        taxId: [shouldNotExceedCharLength(30)],
        phone: [shouldNotExceedCharLength(30), validPhoneNumberFormat],
        fax: [shouldNotExceedCharLength(30), validPhoneNumberFormat],
        email: [shouldNotExceedCharLength(30), validEmailFormat],
        street1: [shouldNotExceedCharLength(100)],
        street2: [shouldNotExceedCharLength(100)],
        city: [shouldNotExceedCharLength(30)],
        province: [shouldNotExceedCharLength(30)],
        country: [shouldNotExceedCharLength(30)],
        zipCode: [shouldNotExceedCharLength(30)],
        note: [shouldNotExceedCharLength(255)],
      },
      errors: [],
    };
  },
  created() {
    this.isLoading = true;
    this.http.http
      .get('api/v2/admin/organization')
      .then(response => {
        const {data} = response.data;
        this.organization.name = data.name;
        this.organization.registrationNumber = data.registrationNumber;
        this.organization.taxId = data.taxId;
        this.organization.phone = data.phone;
        this.organization.fax = data.fax;
        this.organization.email = data.email;
        this.organization.street1 = data.street1;
        this.organization.street2 = data.street2;
        this.organization.city = data.city;
        this.organization.province = data.province;
        if (data.country !== '' && data.country !== null) {
          this.organization.country = this.countryList.find(item => {
            return item.id === data.country;
          });
        }
        this.organization.zipCode = data.zipCode;
        this.organization.note = data.note;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http.http
        .put('api/v2/admin/organization', {
          name: this.organization.name,
          registrationNumber: this.organization.registrationNumber,
          taxId: this.organization.taxId,
          phone: this.organization.phone,
          fax: this.organization.fax,
          email: this.organization.email,
          street1: this.organization.street1,
          street2: this.organization.street2,
          city: this.organization.city,
          province: this.organization.province,
          country: this.organization.country?.id,
          zipCode: this.organization.zipCode,
          note: this.organization.note,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
          this.editable = false;
        });
    },
  },
};
</script>

<style src="./general-info.scss" lang="scss" scoped></style>
