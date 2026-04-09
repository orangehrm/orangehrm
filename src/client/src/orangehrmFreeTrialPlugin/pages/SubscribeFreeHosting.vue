<!--
/*
 * This file is part of OrangeHRM Inc
 *
 * Copyright (C) 2020 onwards OrangeHRM Inc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see  http://www.gnu.org/licenses
 */
-->

<template>
  <div>
    <oxd-alert
      :show="isSubscribed"
      type="error"
      message="You have already subscribed for free-hosting"
    ></oxd-alert>
    <div v-if="!isSubscribed" class="orangehrm-background-container">
      <div class="orangehrm-paper-container">
        <div class="orangehrm-header-container">
          <oxd-text tag="h6" class="orangehrm-main-title"
            >Subscribe For Free Hosting</oxd-text
          >
        </div>
        <div>
          <oxd-divider />
          <oxd-form
            ref="subscribeForm"
            :loading="isLoading"
            @submit-valid="onSubmit"
          >
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field label="System Url" :value="url" disabled />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="subscribe.companyName"
                    :rules="rules.companyName"
                    label="Company Name"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="subscribe.noOfEmployee"
                    :rules="rules.noOfEmployee"
                    placeholder="Employee Count"
                    label="No. of Employees"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="subscribe.country"
                    type="select"
                    :label="$t('general.country')"
                    :options="countries"
                    :rules="rules.country"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="subscribe.contactPersonName"
                    :rules="rules.contactPersonName"
                    label="Contact Person Name"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="subscribe.contactNumber"
                    :rules="rules.contactNumber"
                    label="Contact Number"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="subscribe.email"
                    :rules="rules.email"
                    label="Email"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <div class="orangehrm-action-button">
              <oxd-form-actions>
                <required-text />
                <oxd-button
                  type="button"
                  display-type="ghost"
                  label="Cancel"
                  @click="onCancel"
                />
                <submit-button label="Submit" />
              </oxd-form-actions>
            </div>
          </oxd-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  shouldNotExceedCharLength,
  validPhoneNumberFormat,
  validEmailFormat,
  required,
  greaterThanOrEqual,
  lessThanOrEqual,
} from '@ohrm/core/util/validation/rules';
import {navigate, reloadPage} from '@/core/util/helper/navigation';
import {OxdAlert} from '@ohrm/oxd';

export default {
  name: 'SubscribeFreeHosting',
  components: {
    'oxd-alert': OxdAlert,
  },
  props: {
    url: {
      type: String,
      required: true,
    },
    companyName: {
      type: String,
      required: true,
    },
    contactNumber: {
      type: String,
      required: true,
    },
    email: {
      type: String,
      required: true,
    },
    contactPersonName: {
      type: String,
      required: true,
    },
    country: {
      type: Object,
      default: null,
    },
    isSubscribed: {
      type: Boolean,
      default: false,
    },
    countries: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/trial/subscribeFreeHosting`,
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      subscribe: {
        noOfEmployee: null,
        companyName: '',
        contactNumber: '',
        email: '',
        contactPersonName: '',
        country: null,
        isSubscribed: false,
      },
      rules: {
        email: [required, shouldNotExceedCharLength(320), validEmailFormat],
        contactPersonName: [required, shouldNotExceedCharLength(150)],
        companyName: [required, shouldNotExceedCharLength(255)],
        contactNumber: [
          required,
          shouldNotExceedCharLength(15),
          validPhoneNumberFormat,
        ],
        noOfEmployee: [
          required,
          greaterThanOrEqual(
            1,
            'value should be should be greater than or equal to 1',
          ),
          lessThanOrEqual(999999999, 'value should be less than 1000000000'),
        ],
        country: [required],
      },
    };
  },
  created() {
    this.subscribe.companyName = this.companyName;
    this.subscribe.contactNumber = this.contactNumber;
    this.subscribe.email = this.email;
    this.subscribe.contactPersonName = this.contactPersonName;
    this.subscribe.country = this.country;
    this.subscribe.isSubscribed = this.isSubscribed;
  },
  methods: {
    onSubmit() {
      if (this.subscribe.isSubscribed) {
        navigate('/trial/subscribeFreeHosting');
      }
      if (this.subscribe.noOfEmployee !== null) {
        this.subscribe.noOfEmployee = parseInt(this.subscribe.noOfEmployee);
      }

      this.isLoading = true;
      this.http
        .request({
          method: 'POST',
          data: {
            companyName: this.subscribe.companyName,
            contactNumber: this.subscribe.contactNumber,
            email: this.subscribe.email,
            contactPersonName: this.subscribe.contactPersonName,
            noOfEmployee: this.subscribe.noOfEmployee,
            country: this.subscribe.country.label,
          },
        })
        .then((response) => {
          if (response.status === 200) {
            this.subscribe.companyName = response.data.data.companyName;
            this.subscribe.contactNumber = response.data.data.contactNumber;
            this.subscribe.contactPersonName =
              response.data.data.contactPersonName;
            this.subscribe.country = response.data.data.country;
            this.subscribe.email = response.data.data.email;
            this.subscribe.noOfEmployee = response.data.data.noOfEmployees;
            this.subscribe.isSubscribed = true;
            this.$toast.success({
              title: this.$t('general.success'),
              message: 'Successfully Subscribed',
            });
            navigate('/dashboard/index');
          } else if (response.status === 400) {
            navigate('/trial/subscribeFreeHosting');
          } else if (response.status === 422) {
            navigate('/trial/subscribeFreeHosting');
          }
        });
    },
    onCancel() {
      reloadPage();
    },
  },
};
</script>

<style src="./subscribe-free-hosting.scss" lang="scss" scoped></style>
