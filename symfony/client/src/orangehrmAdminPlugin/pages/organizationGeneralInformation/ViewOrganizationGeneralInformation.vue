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
        <oxd-text tag="h6">General Information</oxd-text>
        <oxd-switch-input
          v-model="editable"
          optionLabel="Edit"
          labelPosition="left"
        />
      </div>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <oxd-input-field
                label="Organization Name"
                v-model="organization.name"
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
                label="Registration Number"
                v-model="organization.registrationNumber"
                :rules="rules.registrationNumber"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Tax ID"
                v-model="organization.taxId"
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
                label="Phone"
                v-model="organization.phone"
                :rules="rules.phone"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Fax"
                v-model="organization.fax"
                :rules="rules.fax"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Email"
                v-model="organization.email"
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
                label="Address Street 1"
                v-model="organization.street1"
                :rules="rules.street1"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Address Street 2"
                v-model="organization.street2"
                :rules="rules.street2"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="City"
                v-model="organization.city"
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
                label="State/Province"
                v-model="organization.province"
                :rules="rules.province"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Zip/Postal Code"
                v-model="organization.zipCode"
                :rules="rules.zipCode"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Country"
                type="dropdown"
                v-model="organization.country"
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
              label="Note"
              type="textarea"
              v-model="organization.note"
              :rules="rules.note"
              :disabled="!editable"
            />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-divider />

        <oxd-form-actions>
          <submit-button v-if="editable" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import SwitchInput from '@orangehrm/oxd/src/core/components/Input/SwitchInput';

export default {
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

  components: {
    'oxd-switch-input': SwitchInput,
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
        name: [
          v => {
            return (!!v && v.trim() !== '') || 'Required';
          },
          v => {
            return v.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        registrationNumber: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        taxId: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        phone: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        fax: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
          v => {
            return !v || v.match(/[0-9+()-]+$/)
              ? true
              : false || 'Allows numbers and only + - / ( )';
          },
        ],
        email: [
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
        country: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        zipCode: [
          v => {
            return !v || v?.length <= 30 || 'Should not exceed 30 characters';
          },
        ],
        note: [
          v => {
            return !v || v?.length <= 255 || 'Should not exceed 255 characters';
          },
        ],
      },
      errors: [],
    };
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
          country: this.organization.country[0]?.id,
          zipCode: this.organization.zipCode,
          note: this.organization.note,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Saved',
          });
        })
        .then(() => {
          this.isLoading = false;
          this.editable = false;
        });
    },
  },
  created() {
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
          this.organization.country = [
            this.countryList.find(c => {
              return c.id === data.country;
            }),
          ];
        }
        this.organization.zipCode = data.zipCode;
        this.organization.note = data.note;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style src="./general-info.scss" lang="scss" scoped></style>
