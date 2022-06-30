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
    <div class="orangehrm-container">
      <div class="orangehrm-card-container">
        <div class="orangehrm-card-container-header">
          <oxd-text class="orangehrm-main-title" tag="h6">
            Apply for the position of "{{ vacancyName }}"
          </oxd-text>
          <img class="oxd-brand-banner" :src="bannerSrc" />
        </div>
        <oxd-divider />
        <oxd-text class="orangehrm-main-title" tag="h2">
          Description
        </oxd-text>
        <div :class="{'orangehrm-vacancy-card-body': isViewDetails}">
          <oxd-text type="toast-message">
            <pre
              v-if="vacancyDescription"
              class="orangehrm-applicant-card-pre-tag"
            >
          {{ vacancyDescription }}
        </pre
            >
          </oxd-text>
        </div>
        <div v-if="vacancyDescription" class="orangehrm-applicant-card-footer">
          <a @click="viewDetails">
            <oxd-text
              class="orangehrm-applicant-card-anchor-tag"
              type="toast-message"
            >
              {{
                isViewDetails
                  ? $t('recruitment.show_more')
                  : $t('recruitment.show_less')
              }}
            </oxd-text>
          </a>
        </div>
        <oxd-divider />
        <oxd-form
          ref="applicantForm"
          :loading="isLoading"
          method="post"
          :action="submitUrl"
          enctype="multipart/form-data"
          @submitValid="onSave"
        >
          <input name="_token" :value="token" type="hidden" />
          <input name="vacancyId" :value="vacancyId" type="hidden" />
          <div class="orangehrm-applicant-container">
            <oxd-form-row class="orangehrm-applicant-container-row">
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <full-name-input
                    v-model:firstName="applicant.firstName"
                    v-model:lastName="applicant.lastName"
                    v-model:middleName="applicant.middleName"
                    :label="$t('general.full_name')"
                    :rules="rules"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row class="orangehrm-applicant-container-row">
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item class="orangehrm-applicant-container-colspan-2">
                  <oxd-input-field
                    v-model="applicant.email"
                    name="email"
                    :label="$t('general.email')"
                    :placeholder="$t('general.type_here')"
                    :rules="rules.email"
                    required
                  />
                </oxd-grid-item>
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="applicant.contactNumber"
                    name="contactNumber"
                    :label="$t('recruitment.contact_number')"
                    :placeholder="$t('general.type_here')"
                    :rules="rules.contactNumber"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="applicant.resume"
                    name="resume"
                    type="file"
                    :label="$t('recruitment.resume')"
                    :button-label="$t('general.browse')"
                    :rules="rules.resume"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-form-row>
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item
                  class="orangehrm-applicant-container-colspan-2 orangehrm-applicant-container-grid-checkbox"
                >
                  <oxd-input-field
                    v-model="applicant.consentToKeepData"
                    name="consentToKeepData"
                    :label="$t('recruitment.consent_to_keep_data')"
                    type="checkbox"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-divider />
            <oxd-form-actions>
              <required-text />
              <oxd-button
                :label="$t('general.back')"
                display-type="ghost"
                @click="onCancel"
              />
              <submit-button />
            </oxd-form-actions>
          </div>
        </oxd-form>
      </div>
      <success-dialogue
        ref="showDialogueModal"
        :title="title"
        :success-label="successLabel"
        :subtitle="subtitle"
      ></success-dialogue>
    </div>
    <div>
      <div class="orangehrm-container-img-div">
        <oxd-text type="toast-message">
          {{ $t('recruitment.powered_by') }}
        </oxd-text>
        <img
          :src="defaultPic"
          alt="OrangeHRM Picture"
          class="orangehrm-container-img"
        />
      </div>
      <div>
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script>
import {ref} from 'vue';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import SuccessDialog from '@/orangehrmRecruitmentPlugin/components/SuccessDialog';
import {
  maxFileSize,
  required,
  shouldNotExceedCharLength,
  validEmailFormat,
  validFileTypes,
  validPhoneNumberFormat,
} from '@ohrm/core/util/validation/rules';
import SubmitButton from '@/core/components/buttons/SubmitButton';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {urlFor} from '@/core/util/helper/url';

const applicantModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  contactNumber: '',
  email: '',
  consentToKeepData: false,
  resume: null,
};

export default {
  name: 'ApplyJobVacancy',
  components: {
    'submit-button': SubmitButton,
    'full-name-input': FullNameInput,
    'success-dialogue': SuccessDialog,
  },
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
    vacancyId: {
      type: Number,
      required: true,
    },
    success: {
      type: Boolean,
      default: false,
    },
    bannerSrc: {
      type: String,
      required: true,
    },
    token: {
      type: String,
      required: true,
    },
  },
  setup() {
    const defaultPic = `${window.appGlobal.baseUrl}/../images/logo.png`;
    const applicant = ref({
      ...applicantModel,
    });
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/recruitment/public/vacancies',
    );
    return {
      applicant,
      http,
      defaultPic,
    };
  },
  data() {
    return {
      title: null,
      successLabel: null,
      subtitle: null,
      viewMore: true,
      isLoading: false,
      vacancyName: null,
      vacancyDescription: null,
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        resume: [
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
        contactNumber: [shouldNotExceedCharLength(25), validPhoneNumberFormat],
        email: [required, validEmailFormat, shouldNotExceedCharLength(50)],
      },
    };
  },
  computed: {
    isViewDetails() {
      return this.viewMore;
    },
    submitUrl() {
      return urlFor('/recruitment/public/applicants');
    },
  },
  beforeMount() {
    this.http.get(this.vacancyId).then(response => {
      const {data} = response.data;
      this.vacancyName = data?.name;
      this.vacancyDescription = data?.description;
    });
    if (this.success) {
      this.title = 'Application Received';
      this.subtitle = 'Your application has been submitted successfully';
      this.successLabel = 'OK';
    }
  },
  mounted() {
    if (this.success) {
      this.showDialogue();
    }
  },
  methods: {
    onSave() {
      this.$refs.applicantForm.$el.submit();
    },
    onCancel() {
      navigate('/recruitment/jobs.html');
    },
    viewDetails() {
      this.viewMore = !this.viewMore;
    },
    showDialogue() {
      this.$refs.showDialogueModal.showSuccessDialog().then(confirmation => {
        if (confirmation === 'ok') {
          navigate('/recruitment/jobs.html');
        }
      });
    },
  },
};
</script>

<style src="./public-job-vacancy.scss" lang="scss" scoped></style>
