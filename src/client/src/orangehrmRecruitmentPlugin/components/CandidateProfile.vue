<template>
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        Candidate Profile
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="1" class="orangehrm-full-width-grid">
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
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <vacancy-dropdown v-model="profile.vacancy" label="job vacancy" />
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
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="profile.contactNumber"
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
                v-model:newFile="profile.newResume"
                v-model:method="profile.method"
                :label="$t('recruitment.resume')"
                :button-label="$t('general.browse')"
                :file="profile.oldResume"
                :rules="rules.resume"
                url="recruitment/resume"
                :hint="$t('general.accepts_up_to_1mb')"
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
                v-model="profile.keywords"
                :label="$t('general.keywords')"
                :placeholder="$t('general.type_here')"
                :rules="rules.keywords"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="profile.applicationDate"
                :label="$t('recruitment.date_of_application')"
                :rules="rules.applicationDate"
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
                v-model="profile.notes"
                :label="$t('general.notes')"
                type="textarea"
                :placeholder="$t('general.type_here')"
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
                v-model="profile.keep"
                type="checkbox"
                :label="$t('recruitment.content_to_keep_data')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <required-text></required-text>
        <oxd-form-actions>
          <oxd-button display-type="ghost" :label="$t('general.cancel')" />
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import {APIService} from '@/core/util/services/api.service';
import {
  shouldNotExceedCharLength,
  required,
  validDateFormat,
  validPhoneNumberFormat,
  validEmailFormat,
} from '@/core/util/validation/rules';
import VacancyDropdown from '@/orangehrmRecruitmentPlugin/components/VacancyDropdown';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import DateInput from '@/core/components/inputs/DateInput';
import {navigate} from '@/core/util/helper/navigation';
export default {
  name: 'CandidateProfile',
  components: {
    DateInput,
    'vacancy-dropdown': VacancyDropdown,
    'file-upload-input': FileUploadInput,
    'full-name-input': FullNameInput,
  },
  props: {
    candidateId: {
      type: Number,
      required: true,
    },
  },
  emits: ['getData'],
  setup(props) {
    const http = new APIService(
      'https://0d188518-fc5f-4b13-833d-5cd0e9fcef79.mock.pstmn.io',
      `/recruitment/candidate/${props.candidateId}`,
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      profile: {
        firstName: '',
        middleName: '',
        lastName: '',
        email: '',
        contactNumber: '',
        oldResume: '',
        notes: '',
        keywords: '',
        newResume: null,
        vacancy: null,
        resume: null,
        method: 'keepCurrent',
        applicationDate: null,
        keep: null,
      },
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        email: [required, validEmailFormat, shouldNotExceedCharLength(50)],
        contactNumber: [validPhoneNumberFormat, shouldNotExceedCharLength(25)],
        keywords: [shouldNotExceedCharLength(250)],
        applicationDate: [validDateFormat()],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then(({data: {data}}) => {
      const {resume, candidate, manager, ...rest} = data;
      const managerName = manager.terminationId
        ? '(Past Employee)'
        : '' + `${manager.firstName} ${manager.middleName} ${manager.lastName}`;
      const fullName = `${candidate.firstName} ${candidate.middleName} ${candidate.lastName}`;
      this.profile.oldResume = resume?.id ? resume : null;
      this.profile.newResume = null;
      this.profile.firstName = candidate.firstName;
      this.profile.middleName = candidate.middleName;
      this.profile.lastName = candidate.lastName;
      this.profile.method = 'keepCurrent';
      this.profile.vacancy = data.vacancy;
      this.profile = {
        ...this.profile,
        ...rest,
      };
      this.isLoading = false;
      this.$emit('getData', {
        stage: [fullName, data.vacancy.label, managerName],
        vacancyId: data.vacancy.id,
      });
    });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.candidateId, this.profile)
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          navigate(`/recruitment/addCandidate/${this.candidateId}`);
        });
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-save-candidate-page {
  &-grid-checkbox {
    .oxd-input-group {
      flex-direction: row-reverse;
      justify-content: flex-end;
    }
  }
}
</style>
