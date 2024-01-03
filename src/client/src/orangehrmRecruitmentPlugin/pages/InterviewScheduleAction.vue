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
    <candidate-action-layout
      v-model:loading="isLoading"
      :candidate-id="candidateId"
      :title="$t('recruitment.schedule_interview')"
      @submit-valid="onSave"
    >
      <oxd-form-row>
        <oxd-grid :cols="3">
          <oxd-grid-item>
            <oxd-input-field
              v-model="interview.interviewName"
              :rules="rules.interviewName"
              :label="$t('recruitment.interview_title')"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <interviewer-autocomplete
              v-for="(interviewer, index) in interviewers"
              :key="index"
              v-model="interviewers[index]"
              :show-delete="index > 0"
              :rules="
                rules.interviewerName.filter((_, i) => index === 0 || i > 0)
              "
              include-employees="onlyCurrent"
              required
              @remove="onRemoveInterviewer(index)"
            />
            <oxd-button
              v-if="interviewers.length < 5"
              icon-name="plus"
              display-type="text"
              class="orangehrm-input-field-bottom-space"
              :label="$t('general.add_another')"
              @click="onAddAnother"
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <date-input
              v-model="interview.interviewDate"
              :rules="rules.interviewDate"
              :label="$t('general.date')"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <time-input
              v-model="interview.interviewTime"
              :rules="rules.interviewTime"
              :label="$t('general.time')"
            />
          </oxd-grid-item>

          <oxd-grid-item class="--offset-row-3 --span-column-2">
            <oxd-input-field
              v-model="interview.note"
              :rules="rules.note"
              :label="$t('general.notes')"
              :placeholder="$t('general.type_here')"
              type="textarea"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
      <required-text></required-text>
      <oxd-form-actions>
        <oxd-button
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onClickBack"
        />
        <submit-button />
      </oxd-form-actions>
    </candidate-action-layout>
  </div>
</template>

<script>
import {
  required,
  validSelection,
  validDateFormat,
  validTimeFormat,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import CandidateActionLayout from '@/orangehrmRecruitmentPlugin/components/CandidateActionLayout.vue';
import InterviewerAutocomplete from '@/orangehrmRecruitmentPlugin/components/InterviewerAutocomplete.vue';
import useDateFormat from '@/core/util/composable/useDateFormat';

const interviewModel = {
  interviewName: null,
  interviewDate: null,
  interviewTime: null,
  note: null,
};

export default {
  components: {
    'candidate-action-layout': CandidateActionLayout,
    'interviewer-autocomplete': InterviewerAutocomplete,
  },
  props: {
    candidateId: {
      type: Number,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/candidates/${props.candidateId}/shedule-interview`,
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
      interview: {...interviewModel},
      interviewers: [null],
      rules: {
        interviewName: [required, shouldNotExceedCharLength(100)],
        interviewDate: [required, validDateFormat(this.userDateFormat)],
        interviewTime: [validTimeFormat],
        interviewerName: [
          required,
          validSelection,
          (value) => {
            return this.interviewers.filter(
              (interviewer) => interviewer && interviewer.id === value?.id,
            ).length < 2
              ? true
              : this.$t('general.already_exists');
          },
        ],
        note: [shouldNotExceedCharLength(2000)],
      },
    };
  },
  methods: {
    onAddAnother() {
      if (this.interviewers.length < 5) {
        this.interviewers.push(null);
      }
    },
    onRemoveInterviewer(index) {
      this.interviewers.splice(index, 1);
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.interview,
          interviewerEmpNumbers: this.interviewers
            .map((interviewer) => interviewer?.id)
            .filter(Number),
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          navigate('/recruitment/addCandidate/{id}', {id: this.candidateId});
        });
    },
    onClickBack() {
      navigate('/recruitment/addCandidate/{id}', {id: this.candidateId});
    },
  },
};
</script>
