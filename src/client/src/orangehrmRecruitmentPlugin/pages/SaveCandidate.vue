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
              <vacancy-dropdown v-model="candidate.vacancy" />
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
              <oxd-input-field
                v-model="candidate.resume"
                type="file"
                :label="$t('recruitment.resume')"
                :button-label="$t('general.browse')"
                :placeholder="$t('general.no_file_chosen')"
                :rules="rules.resume"
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
              <oxd-input-field
                v-model="candidate.application"
                :label="$t('recruitment.date_of_application')"
                :rules="rules.applyDate"
                type="date"
                :placeholder="$t('general.date_format')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                v-model="candidate.notes"
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
                v-model="candidate.keep"
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
import SubmitButton from '@/core/components/buttons/SubmitButton';
import {required, validEmailFormat} from '@/core/util/validation/rules';
import {APIService} from '@ohrm/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';

export default {
  name: 'SaveCandidate',
  components: {
    'submit-button': SubmitButton,
    'vacancy-dropdown': VacancyDropdown,
    'full-name-input': FullNameInput,
  },
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      'https://01eefc6d-daf1-4643-97ae-2d15ea8b587b.mock.pstmn.io',
      'recruitment/api/candidate',
    );
    return {
      http,
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
        contactNumber: '',
        resume: null,
        vacancy: null,
        keywords: '',
        application: '',
        notes: '',
        keep: '',
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
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({data: this.candidate})
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
