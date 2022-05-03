<template>
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-form>
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <full-name-input
                :first-name="candidate.firstName"
                :middle-name="candidate.middleName"
                :last-name="candidate.lastName"
                :rules="rules"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                :label="$t('general.email')"
                placeholder="Type here"
                v-model="candidate.email"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.contact_number')"
                placeholder="Type here"
                v-model="candidate.contactNumber"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-file-input
                button-label="Browse"
                placeholder="No file chosen"
                v-model="candidate.resume"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <vacancy-dropdown v-model="candidate.vacancy" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                :label="'Keywords'"
                placeholder="Type here"
                v-model="candidate.keywords"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="'Date of Application'"
                type="date"
                placeholder="Type here"
                v-model="candidate.application"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                :label="'Notes'"
                type="textarea"
                placeholder="Type here"
                v-model="candidate.notes"
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
                type="checkbox"
                label="Content to keep data"
                v-model="candidate.keep"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button display-type="ghost" :label="$t('general.cancel')" />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {shouldNotExceedCharLength} from '@/core/util/validation/rules';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown';
import SubmitButton from '@/core/components/buttons/SubmitButton';
export default {
  name: 'SaveCandidate',
  components: {SubmitButton, VacancyDropdown, FullNameInput},
  data() {
    return {
      candidate: {
        firstName: '',
        middleName: '',
        lastName: '',
        email: '',
        contactNumber: '',
        resume: '',
        vacancy: null,
        keywords: '',
        application: '',
        notes: '',
        keep: '',
      },
      rules: {
        firstName: [shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [shouldNotExceedCharLength(30)],
      },
    };
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
