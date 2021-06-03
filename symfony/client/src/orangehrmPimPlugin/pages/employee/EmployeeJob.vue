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
                v-model="job.joinedDate"
                :rules="rules.joinedDate"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Job Title"
                v-model="job.jobTitleId"
                :options="jobTitles"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Job Specification"
                v-model="job.jobSpecificationId"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Job Catergory"
                v-model="job.jobCategoryId"
                :options="jobCategories"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Sub Unit"
                v-model="job.subUnitId"
                :options="subunits"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Location"
                v-model="job.locationId"
                :options="countries"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Employment Status"
                v-model="job.empStatusId"
                :options="employmentStatuses"
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
                  v-model="job.startDate"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <oxd-input-field
                  label="Contract End Date"
                  v-model="job.endDate"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <oxd-input-field
                  label="Contract Details"
                  v-model="job.contractAttachment"
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

const ContractDetailsModel = {
  joinedDate: '',
  endDate: '',
  contractAttachment: '',
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
    subunits: {
      type: Array,
      default: () => [],
    },
    employmentStatuses: {
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
      job: {...jobDetailsModel},
      Contract: {...ContractDetailsModel},
      rules: {
        joinedDate: [
          v => {
            return !v || v?.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        endDate: [
          v => {
            return !v || v?.length <= 100 || 'Should not exceed 100 characters';
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
            ...this.job,
            locationId: this.job.locationId.map(item => item.id)[0],
            jobTitleId: this.job.jobTitleId.map(item => item.id)[0],
            jobCategoryId: this.job.jobCategoryId.map(item => item.id)[0],
            subunitId: this.job.subunitId.map(item => item.id)[0],
            empStatusId: this.job.empStatusId.map(item => item.id)[0],
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
      this.Contract = {...ContractDetailsModel, ...data};
      this.job = {...jobDetailsModel, ...data};
      this.job.locationId = this.countries.filter(
        item => item.id === data.locationId,
      );
      this.job.jobTitleId = this.jobTitles.filter(
        item => item.id === data.jobTitleId?.id,
      );
      this.job.jobCategoryId = this.jobCategories.filter(
        item => item.id === data.jobCategoryId?.id,
      );
      this.job.subunitId = this.subunits.filter(
        item => item.id === data.subunitId?.id,
      );
      this.job.empStatusId = this.employmentStatuses.filter(
        item => item.id === data.empStatusId?.id,
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
