<template>
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        Candidate Profile
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <full-name-input
                v-model:first-name="profile.firstName"
                v-model:middle-name="profile.middleName"
                v-model:last-name="profile.lastName"
                :rules="rules"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
            <oxd-grid-item>
             <vacancy-dropdown label="job vacancy"/>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
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
  required,
  validDateFormat,
  validFileTypes,
  validPhoneNumberFormat,
} from '@/core/util/validation/rules';
import VacancyDropdown from "@/orangehrmRecruitmentPlugin/components/VacancyDropdown";
export default {
  name: 'CandidateProfile',
  components:{
    VacancyDropdown,
    'full-name-input':FullNameInput
  },
  data() {
    return {
      isLoading: false,
      profile: {
        firstName: '',
        middleName: '',
        lastName: '',
      },
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
      },
    };
  },
  methods: {
    onSave() {
      console.log('save');
    },
  },
};
</script>

<style scoped></style>
