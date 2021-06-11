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
      <oxd-text tag="h6" class="orangehrm-main-title">Job Details</oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Joined Date"
                v-model="job.joinedDate"
                :rules="rules.joinedDate"
                type="date"
                placeholder="yyyy-mm-dd"
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
              <job-spec-download
                :key="`jobspec-${selectedJobTitleId}`"
                :resource-id="selectedJobTitleId"
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
                v-model="job.subunitId"
                :options="subunits"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="dropdown"
                label="Location"
                v-model="job.locationId"
                :options="locations"
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

        <oxd-form-row class="user-form-header">
          <oxd-text class="user-form-header-text" tag="p"
            >Include Employment Contract Details</oxd-text
          >
          <oxd-switch-input v-model="showContractDetails" />
        </oxd-form-row>

        <template v-if="showContractDetails">
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <oxd-input-field
                  label="Contract Start Date"
                  v-model="contract.startDate"
                  :rules="rules.startDate"
                  type="date"
                  placeholder="yyyy-mm-dd"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <oxd-input-field
                  label="Contract End Date"
                  v-model="contract.endDate"
                  :rules="rules.endDate"
                  type="date"
                  placeholder="yyyy-mm-dd"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <file-upload-input
                  label="Contract Details"
                  buttonLabel="Browse"
                  v-model:newFile="contract.newAttachment"
                  v-model:method="contract.method"
                  :file="contract.oldAttachment"
                  :rules="rules.contractAttachment"
                  :url="`pim/viewAttachment/empNumber/${empNumber}/attachId`"
                  hint="Accepts up to 1MB"
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
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import SwitchInput from '@orangehrm/oxd/core/components/Input/SwitchInput';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import JobSpecDownload from '@/orangehrmPimPlugin/components/JobSpecDownload';

const jobDetailsModel = {
  joinedDate: '',
  jobTitleId: [],
  empStatusId: [],
  jobCategoryId: [],
  subunitId: [],
  locationId: [],
};

const contractDetailsModel = {
  startDate: '',
  endDate: '',
  oldAttachment: null,
  newAttachment: null,
  method: 'keepCurrent',
};

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
    'oxd-switch-input': SwitchInput,
    'job-spec-download': JobSpecDownload,
    'file-upload-input': FileUploadInput,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    locations: {
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
      `api/v2/pim/employees/${props.empNumber}/job-details`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      showContractDetails: false,
      job: {...jobDetailsModel},
      contract: {...contractDetailsModel},
      rules: {
        startDate: [],
        endDate: [],
        contractAttachment: [
          v => {
            if (this.contract.method == 'replaceCurrent') {
              return !!v || 'Required';
            } else {
              return true;
            }
          },
          v =>
            v === null ||
            (v && v.size && v.size <= 1024 * 1024) ||
            'Attachment size exceeded',
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
            jobTitleId: this.job.jobTitleId.map(item => item.id)[0],
            jobCategoryId: this.job.jobCategoryId.map(item => item.id)[0],
            subunitId: this.job.subunitId.map(item => item.id)[0],
            empStatusId: this.job.empStatusId.map(item => item.id)[0],
            locationId: this.job.locationId.map(item => item.id)[0],
          },
        })
        .then(response => {
          this.updateJobModel(response);
          return this.http.request({
            method: 'PUT',
            url: `api/v2/pim/employees/${this.empNumber}/employment-contract`,
            data: {
              startDate: this.contract.startDate,
              endDate: this.contract.endDate,
              currentContractAttachment:
                this.contract.method != 'keepCurrent'
                  ? this.contract.method
                  : undefined,
              contractAttachment: this.contract.newAttachment
                ? this.contract.newAttachment
                : undefined,
            },
          });
        })
        .then(response => {
          if (response) {
            this.updateContractModel(response);
          }
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateContractModel(response) {
      const {data} = response.data;
      this.contract.startDate = data.startDate;
      this.contract.endDate = data.endDate;
      this.contract.oldAttachment = data.contractAttachment?.id
        ? data.contractAttachment
        : null;
      this.contract.newAttachment = null;
      this.contract.method = 'keepCurrent';
    },

    updateJobModel(response) {
      const {data} = response.data;
      this.job.joinedDate = data.joinedDate;
      this.job.jobTitleId = this.jobTitles.filter(
        item => item.id === data.jobTitle?.id,
      );
      this.job.jobCategoryId = this.jobCategories.filter(
        item => item.id === data.jobCategory?.id,
      );
      this.job.subunitId = this.subunits.filter(
        item => item.id === data.subunit?.id,
      );
      this.job.empStatusId = this.employmentStatuses.filter(
        item => item.id === data.empStatus?.id,
      );
      this.job.locationId = this.locations.filter(
        item => item.id === data.location?.id,
      );
    },
  },

  computed: {
    selectedJobTitleId() {
      const jobTitleId = this.job.jobTitleId.map(item => item.id)[0];
      return jobTitleId || 0;
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        this.updateJobModel(response);
      })
      .then(() => {
        return this.http.request({
          method: 'GET',
          url: `api/v2/pim/employees/${this.empNumber}/employment-contract`,
        });
      })
      .then(response => {
        this.updateContractModel(response);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
