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
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('recruitment.add_candidate') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <full-name-input
                v-model:first-name="candidate.firstName"
                v-model:middle-name="candidate.middleName"
                v-model:last-name="candidate.lastName"
                :label="$t('general.full_name')"
                :rules="rules"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <vacancy-dropdown
                v-model="candidate.vacancyId"
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
                v-model="candidate.email"
                :label="$t('general.email')"
                :placeholder="$t('general.type_here')"
                :rules="rules.email"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="candidate.contactNumber"
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
              <file-upload-input
                v-model:newFile="resume.newAttachment"
                v-model:method="resume.method"
                :label="$t('recruitment.resume')"
                :button-label="$t('general.browse')"
                :file="resume.oldAttachment"
                :rules="rules.resume"
                :hint="
                  $t('general.accept_custom_format_file_up_to_n_mb', {
                    count: formattedFileSize,
                  })
                "
                url="recruitment/candidateAttachment/attachId"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                v-model="candidate.keywords"
                :label="$t('recruitment.keywords')"
                :placeholder="`${$t(
                  'recruitment.enter_comma_seperated_words',
                )}...`"
                :rules="rules.keywords"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="candidate.dateOfApplication"
                :label="$t('recruitment.date_of_application')"
                :rules="rules.applyDate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                v-model="candidate.comment"
                :label="$t('general.notes')"
                type="textarea"
                :placeholder="$t('general.type_here')"
                :rules="rules.notes"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page-full-width orangehrm-save-candidate-page-grid-checkbox"
            >
              <oxd-input-field
                v-model="candidate.consentToKeepData"
                type="checkbox"
                :label="$t('recruitment.consent_to_keep_data')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
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
  shouldBeCurrentOrPreviousDate,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import SubmitButton from '@/core/components/buttons/SubmitButton';
import {freshDate, formatDate} from '@ohrm/core/util/helper/datefns';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown';
import useDateFormat from '@/core/util/composable/useDateFormat';

export default {
  name: 'SaveCandidate',
  components: {
    'submit-button': SubmitButton,
    'full-name-input': FullNameInput,
    'vacancy-dropdown': VacancyDropdown,
    'file-upload-input': FileUploadInput,
  },
  props: {
    maxFileSize: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/candidates',
    );
    const {userDateFormat} = useDateFormat();

    return {
      http,
      userDateFormat,
    };
  },
  data() {
    return {
      isLoading: false,
      candidate: {
        firstName: null,
        middleName: null,
        lastName: null,
        email: null,
        contactNumber: null,
        keywords: null,
        comment: null,
        dateOfApplication: formatDate(freshDate(), 'yyyy-MM-dd'),
        consentToKeepData: false,
        vacancyId: null,
      },
      resume: {
        id: null,
        oldAttachment: null,
        newAttachment: null,
        method: 'keepCurrent',
      },
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        email: [required, validEmailFormat, shouldNotExceedCharLength(50)],
        contactNumber: [validPhoneNumberFormat, shouldNotExceedCharLength(25)],
        notes: [shouldNotExceedCharLength(250)],
        keywords: [shouldNotExceedCharLength(250)],
        resume: [
          maxFileSize(this.maxFileSize),
          validFileTypes(this.allowedFileTypes),
        ],
        applyDate: [
          validDateFormat(this.userDateFormat),
          shouldBeCurrentOrPreviousDate(),
        ],
      },
    };
  },
  computed: {
    formattedFileSize() {
      return Math.round((this.maxFileSize / (1024 * 1024)) * 100) / 100;
    },
  },
  methods: {
    onSave() {
      let candidateId;
      this.isLoading = true;
      this.http
        .create({...this.candidate, vacancyId: this.candidate.vacancyId?.id})
        .then(({data: {data}}) => {
          candidateId = parseInt(data.id);
          if (!this.resume.newAttachment) return;
          return this.http.request({
            method: 'POST',
            url: '/api/v2/recruitment/candidate/attachments',
            data: {
              candidateId,
              attachment: this.resume.newAttachment,
            },
          });
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate('/recruitment/addCandidate/{id}', {id: candidateId});
        });
    },
    onCancel() {
      navigate('/recruitment/viewCandidates');
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-save-candidate-page {
  &-full-width {
    grid-column: 1 / span 2;
  }

  &-grid-checkbox {
    .oxd-input-group {
      flex-direction: row-reverse;
      justify-content: flex-end;
    }
  }
}
</style>
