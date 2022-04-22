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
  <edit-employee-layout
    :employee-id="empNumber"
    screen="job"
    :allowed-file-types="allowedFileTypes"
  >
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('pim.job_details') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                v-model="job.joinedDate"
                :label="$t('general.joined_date')"
                :rules="rules.joinedDate"
                :disabled="!hasUpdatePermissions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="job.jobTitleId"
                type="select"
                :label="$t('general.job_title')"
                :options="normalizedJobTitles"
                :disabled="!hasUpdatePermissions"
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
                v-model="job.jobCategoryId"
                type="select"
                :label="$t('general.job_category')"
                :options="jobCategories"
                :disabled="!hasUpdatePermissions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="job.subunitId"
                type="select"
                :label="$t('general.sub_unit')"
                :options="subunits"
                :disabled="!hasUpdatePermissions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="job.locationId"
                type="select"
                :label="$t('general.location')"
                :options="locations"
                :disabled="!hasUpdatePermissions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="job.empStatusId"
                type="select"
                :label="$t('general.employment_status')"
                :options="employmentStatuses"
                :disabled="!hasUpdatePermissions"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-row class="user-form-header">
          <oxd-text class="user-form-header-text" tag="p">
            {{ $t('pim.include_employment_contract_details') }}
          </oxd-text>
          <oxd-switch-input v-model="showContractDetails" />
        </oxd-form-row>

        <template v-if="showContractDetails">
          <oxd-form-row>
            <oxd-grid :cols="3" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <date-input
                  v-model="contract.startDate"
                  :label="$t('pim.contract_start_date')"
                  :rules="rules.startDate"
                  :disabled="!hasUpdatePermissions"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <date-input
                  v-model="contract.endDate"
                  :label="$t('pim.contract_end_date')"
                  :rules="rules.endDate"
                  :disabled="!hasUpdatePermissions"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <file-upload-input
                  v-model:newFile="contract.newAttachment"
                  v-model:method="contract.method"
                  :label="$t('pim.contract_details')"
                  :button-label="$t('general.browse')"
                  :file="contract.oldAttachment"
                  :rules="rules.contractAttachment"
                  :url="`pim/viewAttachment/empNumber/${empNumber}/attachId`"
                  hint="Accepts up to 1MB"
                  :disabled="!hasUpdatePermissions"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
        </template>

        <template v-if="hasUpdatePermissions">
          <oxd-divider />
          <oxd-form-actions>
            <submit-button />
          </oxd-form-actions>
        </template>
      </oxd-form>
    </div>

    <oxd-divider v-if="hasUpdatePermissions" />

    <div
      v-if="hasUpdatePermissions"
      class="orangehrm-horizontal-padding orangehrm-vertical-padding"
    >
      <profile-action-header
        icon-name=""
        :display-type="terminationActionType"
        :label="terminationActionLabel"
        class="--termination-button"
        @click="onClickTerminate"
      >
        {{ $t('pim.employee_termination_activation') }}
      </profile-action-header>
      <oxd-text
        v-if="termination && termination.id"
        tag="p"
        class="orangehrm-terminate-date"
        @click="openTerminateModal"
      >
        {{ $t('pim.terminated_on') }}: {{ termination.date }}
      </oxd-text>
    </div>
    <terminate-modal
      v-if="showTerminateModal"
      :employee-id="empNumber"
      :termination-reasons="terminationReasons"
      :termination-id="termination.id"
      @close="closeTerminateModal"
    ></terminate-modal>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import JobSpecDownload from '@/orangehrmPimPlugin/components/JobSpecDownload';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader';
import TerminateModal from '@/orangehrmPimPlugin/components/TerminateModal';
import {
  required,
  maxFileSize,
  validFileTypes,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';

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
    'profile-action-header': ProfileActionHeader,
    'terminate-modal': TerminateModal,
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
    terminationReasons: {
      type: Array,
      default: () => [],
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
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
      termination: null,
      showTerminateModal: false,
      rules: {
        startDate: [validDateFormat()],
        endDate: [
          validDateFormat(),
          endDateShouldBeAfterStartDate(() => this.contract.startDate),
        ],
        contractAttachment: [
          v => {
            if (this.contract.method == 'replaceCurrent') {
              return required(v);
            } else {
              return true;
            }
          },
          validFileTypes(this.allowedFileTypes),
          maxFileSize(this.maxFileSize),
        ],
      },
    };
  },

  computed: {
    selectedJobTitleId() {
      const jobTitleId = this.job.jobTitleId?.id;
      return jobTitleId || 0;
    },
    terminationActionLabel() {
      return this.termination?.id
        ? this.$t('pim.activate_employment')
        : this.$t('pim.terminate_employment');
    },
    terminationActionType() {
      return this.termination?.id ? 'ghost-success' : 'label-danger';
    },
    hasUpdatePermissions() {
      return this.$can.update(`job_details`);
    },
    normalizedJobTitles() {
      return this.jobTitles.map(jobTitle => {
        return {
          id: jobTitle.id,
          label: jobTitle?.deleted
            ? jobTitle.label + ' (Deleted)'
            : jobTitle.label,
        };
      });
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

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            ...this.job,
            jobTitleId: this.job.jobTitleId?.id,
            jobCategoryId: this.job.jobCategoryId?.id,
            subunitId: this.job.subunitId?.id,
            empStatusId: this.job.empStatusId?.id,
            locationId: this.job.locationId?.id,
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
              currentContractAttachment: this.contract.oldAttachment
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

    onClickTerminate() {
      if (this.termination?.id) {
        this.$loader.startLoading();
        this.http
          .request({
            method: 'DELETE',
            url: `api/v2/pim/employees/${this.empNumber}/terminations`,
          })
          .then(() => {
            return this.$toast.updateSuccess();
          })
          .then(() => {
            this.$loader.endLoading();
            location.reload();
          });
      } else {
        this.openTerminateModal();
      }
    },

    openTerminateModal() {
      this.showTerminateModal = true;
    },

    closeTerminateModal(reload) {
      this.showTerminateModal = false;
      if (reload) {
        location.reload();
      }
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
      if (data.startDate || data.endDate || data.contractAttachment?.id) {
        this.showContractDetails = true;
      } else {
        this.showContractDetails = false;
      }
    },

    updateJobModel(response) {
      const {data} = response.data;
      this.job.joinedDate = data.joinedDate;
      this.job.jobTitleId = this.normalizedJobTitles.find(
        item => item.id === data.jobTitle?.id,
      );
      this.job.jobCategoryId = this.jobCategories.find(
        item => item.id === data.jobCategory?.id,
      );
      this.job.subunitId = this.subunits.find(
        item => item.id === data.subunit?.id,
      );
      this.job.empStatusId = this.employmentStatuses.find(
        item => item.id === data.empStatus?.id,
      );
      this.job.locationId = this.locations.find(
        item => item.id === data.location?.id,
      );
      this.termination = data.employeeTerminationRecord;
    },
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
