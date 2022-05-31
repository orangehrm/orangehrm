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
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title"
        >{{ $t('recruitment.add_candidate') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <full-name-input
                v-model:first-name="candidate.firstName"
                v-model:middle-name="candidate.middleName"
                v-model:last-name="candidate.lastName"
                :rules="rules"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <vacancy-dropdown v-model="candidate.vacancyId" />
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
                :url="`recruitment/candidateAttachment/attachId`"
                :hint="$t('general.accepts_up_to_1mb')"
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
                :placeholder="$t('recruitment.enter_comma_se')"
                :rules="rules.keywords"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="candidate.dateOfApplication"
                :label="$t('recruitment.date_of_application')"
                :rules="rules.date"
                :placeholder="$t('general.date_format')"
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
                :label="$t('recruitment.content_to_keep_data')"
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
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {
  maxFileSize,
  shouldBeCurrentOrPreviousDate,
  shouldNotExceedCharLength,
  validDateFormat,
  validFileTypes,
  validPhoneNumberFormat,
} from '@/core/util/validation/rules';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import SubmitButton from '@/core/components/buttons/SubmitButton';
import {required, validEmailFormat} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
import {parseDate, formatDate} from '@/core/util/helper/datefns';

export default {
  name: 'SaveCandidate',
  components: {
    'submit-button': SubmitButton,
    'vacancy-dropdown': VacancyDropdown,
    'full-name-input': FullNameInput,
    'file-upload-input': FileUploadInput,
  },
  props: {
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
    const httpAttachments = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/candidate/attachments',
    );
    return {
      http,
      httpAttachments,
    };
  },
  data() {
    return {
      isLoading: false,
      candidate: {
        firstName: null,
        middleName: '',
        lastName: '',
        email: '',
        contactNumber: null,
        keywords: null,
        comment: null,
        dateOfApplication: '',
        modeOfApplication: 1,
        consentToKeepData: false,
        status: 1,
        vacancyId: null,
      },
      resume: {
        id: null,
        oldAttachment: {},
        newAttachment: null,
        method: 'replaceCurrent',
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
          maxFileSize(1024 * 1024),
          validFileTypes(this.allowedFileTypes),
        ],
        applyDate: [validDateFormat(), shouldBeCurrentOrPreviousDate()],
      },
    };
  },
  beforeMount() {
    this.setCurrentDateTime();
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({...this.candidate, vacancyId: this.candidate.vacancyId?.id})
        .then(response => {
          if (!this.resume.newAttachment) {
            return true;
          }
          const {data} = response.data;
          return this.httpAttachments.create({
            candidateId: parseInt(data.id),
            attachment: this.resume.newAttachment,
          });
        })
        .then(() => {
          this.isLoading = false;
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate('/recruitment/viewCandidates');
        });
    },
    onCancel() {
      navigate('/recruitment/viewCandidates');
    },
    setCurrentDateTime() {
      return new Promise((resolve, reject) => {
        this.http
          .request({method: 'GET', url: '/api/v2/attendance/current-datetime'})
          .then(res => {
            const {utcDate, utcTime} = res.data.data;
            const currentDate = parseDate(
              `${utcDate} ${utcTime} +00:00`,
              'yyyy-MM-dd HH:mm xxx',
            );
            this.candidate.dateOfApplication =
              this.date ?? formatDate(currentDate, 'yyyy-MM-dd');
            resolve();
          })
          .catch(error => reject(error));
      });
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
