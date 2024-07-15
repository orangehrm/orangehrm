<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('recruitment.candidate_profile') }}
        </oxd-text>
        <oxd-switch-input
          v-if="!isLoading && updatable"
          v-model="editable"
          :option-label="$t('general.edit')"
          label-position="left"
        />
      </div>

      <oxd-divider v-show="!isLoading" />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <full-name-input
                v-model:first-name="profile.firstName"
                v-model:middle-name="profile.middleName"
                v-model:last-name="profile.lastName"
                :rules="rules"
                :label="$t('general.full_name')"
                :disabled="!editable"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <vacancy-dropdown
                v-model="vacancy"
                :label="$t('recruitment.job_vacancy')"
                :readonly="!editable"
                :exclude-interviewers="true"
                :status="true"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="profile.email"
                :label="$t('general.email')"
                :placeholder="$t('general.type_here')"
                :rules="rules.email"
                :disabled="!editable"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="profile.contactNumber"
                :label="$t('recruitment.contact_number')"
                :placeholder="$t('general.type_here')"
                :rules="rules.contactNumber"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider></oxd-divider>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <file-upload-input
                v-model:newFile="attachment.newAttachment"
                v-model:method="attachment.method"
                :label="$t('recruitment.resume')"
                :button-label="$t('general.browse')"
                :file="attachment.oldAttachment"
                :rules="rules.resume"
                :hint="
                  $t('general.accept_custom_format_file_up_to_n_mb', {
                    count: formattedFileSize,
                  })
                "
                :disabled="!editable"
                :url="getResumeUrl"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider></oxd-divider>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page --span-column-2"
            >
              <oxd-input-field
                v-model="profile.keywords"
                :label="$t('recruitment.keywords')"
                :placeholder="`${$t(
                  'recruitment.enter_comma_seperated_words',
                )}...`"
                :rules="rules.keywords"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="profile.dateOfApplication"
                :label="$t('recruitment.date_of_application')"
                :rules="rules.applicationDate"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page --span-column-2"
            >
              <oxd-input-field
                v-model="profile.comment"
                :label="$t('general.notes')"
                type="textarea"
                :placeholder="$t('general.type_here')"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-candidate-grid-checkbox">
              <oxd-input-field
                v-model="profile.consentToKeepData"
                type="checkbox"
                :label="$t('recruitment.consent_to_keep_data')"
                :disabled="!editable"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider v-show="editable" />

        <oxd-form-actions v-if="editable">
          <required-text></required-text>
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>

    <confirmation-dialog
      ref="confirmDialog"
      :title="$t('general.confirmation_required')"
      :subtitle="$t('recruitment.candidate_vacancy_change_message')"
      :cancel-label="$t('general.no_cancel')"
      :confirm-label="$t('leave.yes_confirm')"
      confirm-button-type="secondary"
    ></confirmation-dialog>
  </div>
</template>

<script>
import {
  required,
  maxFileSize,
  validFileTypes,
  validDateFormat,
  validEmailFormat,
  validPhoneNumberFormat,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {urlFor} from '@ohrm/core/util/helper/url';
import DateInput from '@/core/components/inputs/DateInput';
import {APIService} from '@/core/util/services/api.service';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown';
import useDateFormat from '@/core/util/composable/useDateFormat';
import ConfirmationDialog from '@/core/components/dialogs/ConfirmationDialog';
import {OxdSwitchInput} from '@ohrm/oxd';

const CandidateProfileModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  email: '',
  contactNumber: '',
  comment: '',
  keywords: '',
  dateOfApplication: null,
  consentToKeepData: false,
};

const CandidateAttachmentModel = {
  id: null,
  oldAttachment: {},
  newAttachment: null,
  method: 'replaceCurrent',
};

const VacancyModel = {
  id: null,
  label: '',
};

export default {
  name: 'CandidateProfile',
  components: {
    DateInput,
    'oxd-switch-input': OxdSwitchInput,
    'full-name-input': FullNameInput,
    'vacancy-dropdown': VacancyDropdown,
    'file-upload-input': FileUploadInput,
    'confirmation-dialog': ConfirmationDialog,
  },
  props: {
    candidate: {
      type: Object,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
    updatable: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
  emits: ['update'],
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '/');
    const {userDateFormat} = useDateFormat();

    return {
      http,
      userDateFormat,
    };
  },
  data() {
    return {
      editable: false,
      isLoading: false,
      profile: {...CandidateProfileModel},
      vacancy: {...VacancyModel},
      attachment: {...CandidateAttachmentModel},
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        email: [required, validEmailFormat, shouldNotExceedCharLength(50)],
        contactNumber: [validPhoneNumberFormat, shouldNotExceedCharLength(25)],
        keywords: [shouldNotExceedCharLength(250)],
        applicationDate: [validDateFormat(this.userDateFormat)],
        resume: [
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
      },
    };
  },
  computed: {
    formattedFileSize() {
      return Math.round((this.maxFileSize / (1024 * 1024)) * 100) / 100;
    },
  },
  watch: {
    candidate() {
      this.fetchCandidate();
    },
  },
  beforeMount() {
    this.fetchCandidate();
  },
  methods: {
    onSave() {
      if (
        this.candidate.vacancy?.id &&
        this.candidate.vacancy?.id !== this.vacancy?.id
      ) {
        this.$refs.confirmDialog.showDialog().then((confirmation) => {
          if (confirmation === 'ok') this.updateCandidate();
        });
      } else {
        this.updateCandidate();
      }
    },
    updateCandidate() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: `/api/v2/recruitment/candidates/${this.candidate.id}`,
          data: {...this.profile, vacancyId: this.vacancy?.id},
        })
        .then(() => {
          if (this.attachment.newAttachment || this.candidate.hasAttachment) {
            return this.http.request({
              method: 'PUT',
              url: `/api/v2/recruitment/candidate/${this.candidate.id}/attachment`,
              data: {
                currentAttachment: this.attachment.oldAttachment
                  ? this.attachment.method
                  : undefined,
                attachment: this.attachment.newAttachment
                  ? this.attachment.newAttachment
                  : undefined,
              },
            });
          }
          return true;
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.$emit('update');
          this.isLoading = false;
          this.editable = false;
        });
    },
    getResumeUrl() {
      return urlFor(
        '/recruitment/viewCandidateAttachment/candidateId/{candidateId}',
        {candidateId: this.candidate.id},
      );
    },
    fetchCandidate() {
      this.isLoading = true;
      this.profile.firstName = this.candidate.firstName;
      this.profile.middleName = this.candidate.middleName;
      this.profile.lastName = this.candidate.lastName;
      this.profile.email = this.candidate.email;
      this.profile.contactNumber = this.candidate.contactNumber;
      this.profile.keywords = this.candidate.keywords;
      this.profile.dateOfApplication = this.candidate.dateOfApplication;
      this.profile.comment = this.candidate.comment;
      this.profile.consentToKeepData = this.candidate.consentToKeepData;
      const {vacancy} = this.candidate;
      if (vacancy) {
        this.vacancy = {
          id: vacancy.id,
          label:
            vacancy.status === false
              ? vacancy.name + ` (${this.$t('general.closed')})`
              : vacancy.name,
        };
      }
      if (this.candidate.hasAttachment) {
        this.http
          .request({
            method: 'GET',
            url: `/api/v2/recruitment/candidate/${this.candidate.id}/attachment`,
          })
          .then(({data: {data}}) => {
            this.attachment.id = data.id;
            this.attachment.newAttachment = null;
            this.attachment.oldAttachment = {
              id: data.id,
              filename: data.attachment.fileName,
              fileType: data.attachment.fileType,
              fileSize: data.attachment.fileSize,
            };
            this.attachment.method = 'keepCurrent';
          });
      } else {
        this.attachment = {...CandidateAttachmentModel};
      }
      this.isLoading = false;
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-header-container {
  padding: 0;
}
.orangehrm-candidate-grid-checkbox {
  .oxd-input-group {
    flex-direction: row-reverse;
    justify-content: flex-end;
  }
}
</style>
