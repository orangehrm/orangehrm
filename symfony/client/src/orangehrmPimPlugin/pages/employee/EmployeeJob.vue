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
  <edit-employee-layout :employee-id="empNumber" screen="job">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6">Job Details</oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Joined Date"
                v-model="contract.joinedDate"
                :rules="rules.joinedDate"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Job Title"
                v-model="contract.jobTitleId"
                :options="jobTitles"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Job Specification"
                v-model="contract.jobSpecificationId"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Job Catergory"
                v-model="contract.jobCategoryId"
                :options="jobCategories"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Sub Unit"
                v-model="contract.subUnitId"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Location"
                v-model="contract.locationId"
                :options="countries"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Employment Status"
                v-model="contract.locationId"
                :options="countries"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-row class="contract-form-header">
          <oxd-text class="contract-form-header-text" tag="p"
            >Include Employement Contract Details
          </oxd-text>
          <oxd-switch-input v-model="createContractDetails" />
        </oxd-form-row>

        <template v-if="createContractDetails">
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  label="Contract Start Date"
                  v-model="contract.city"
                  :rules="rules.city"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <oxd-input-field
                  label="Contract End Date"
                  v-model="contract.city"
                  :rules="rules.city"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <oxd-input-field
                  label="Contract Details"
                  v-model="contract.city"
                  :rules="rules.city"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
        </template>

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
import SwitchInput from '@orangehrm/oxd/core/components/Input/SwitchInput';

const jobDetailsModel = {
  joinedDate: '',
  jobTitleId: [],
  empStatusId: [],
  jobCategoryId: [],
  subunitId: [],
  locationId: [],
};

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
    'oxd-switch-input': SwitchInput,
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
    jobTitles: {
      type: Array,
      default: () => [],
    },
    jobCategories: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/pim/employee/${props.empNumber}/job-details`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      createContractDetails: false,
      contract: {...jobDetailsModel},
      rules: {
        joinedDate: [
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
        homeTelephone: [
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
        workEmail: [
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
        otherEmail: [
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

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            ...this.contract,
            locationId: this.contract.locationId.map(item => item.id)[0],
            jobTitleId: this.contract.jobTitleId.map(item => item.id)[0],
            jobCategoryId: this.contract.jobCategoryId.map(
              item => item.id,
            )[0],
          },
        })
        .then(response => {
          this.updateModel(response);
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Updated',
          });
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateModel(response) {
      const {data} = response.data;
      this.contract = {...jobDetailsModel, ...data};
      this.contract.locationId = this.countries.filter(
        item => item.id === data.locationId,
      );
      this.contract.jobTitleId = this.jobTitles.filter(
        item => item.id === data.jobTitleId?.id,
      );
      this.contract.jobCategoryId = this.jobCategories.filter(
        item => item.id === data.jobCategoryId?.id,
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
