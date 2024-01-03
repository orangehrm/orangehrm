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
      :loading="isLoading"
      :candidate-id="candidateId"
      :title="$t('recruitment.view_action_history')"
      @submit-valid="onSave"
    >
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              :label="$t('recruitment.performed_action')"
              :value="performedAction"
              readonly
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              :label="$t('general.performed_by')"
              :value="performedBy"
              readonly
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              :label="$t('recruitment.performed_date')"
              :value="performedDate"
              readonly
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row v-if="isScheduleInterview">
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
              :disabled="!editable"
              @remove="onRemoveInterviewer(index)"
            />
            <oxd-button
              v-if="interviewers.length < 5 && editable"
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
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3">
          <oxd-grid-item class="--span-column-2">
            <oxd-input-field
              v-model="history.note"
              :rules="rules.note"
              :label="$t('general.notes')"
              :placeholder="$t('general.type_here')"
              type="textarea"
              :disabled="disabled"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
      <oxd-form-actions>
        <oxd-button
          display-type="ghost"
          :label="$t('general.back')"
          @click="onClickBack"
        />
        <submit-button v-if="!disabled" :label="$t('general.save')" />
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
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import CandidateActionLayout from '@/orangehrmRecruitmentPlugin/components/CandidateActionLayout.vue';
import InterviewerAutocomplete from '@/orangehrmRecruitmentPlugin/components/InterviewerAutocomplete.vue';

const actionHistoryModel = {
  id: null,
  action: {
    id: null,
    label: null,
  },
  performedBy: {
    empNumber: null,
    lastName: null,
    firstName: null,
    middleName: null,
    terminationId: null,
  },
  interview: {
    id: null,
  },
  performedDate: null,
  note: null,
};

const interviewModel = {
  interviewName: null,
  interviewDate: null,
  interviewTime: null,
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
    historyId: {
      type: Number,
      required: true,
    },
    editable: {
      type: Boolean,
      required: false,
      default: true,
    },
  },

  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat, userDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/candidates/${props.candidateId}/history`,
    );

    return {
      http,
      locale,
      jsDateFormat,
      userDateFormat,
      translateEmpName: $tEmpName,
    };
  },

  data() {
    return {
      isLoading: false,
      history: {...actionHistoryModel},
      interview: {...interviewModel},
      interviewers: [],
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
      statuses: [
        {id: 1, label: this.$t('recruitment.application_initiated')},
        {id: 2, label: this.$t('recruitment.shortlist')},
        {id: 3, label: this.$t('general.reject')},
        {id: 4, label: this.$t('recruitment.schedule_interview')},
        {id: 5, label: this.$t('recruitment.mark_interview_passed')},
        {id: 6, label: this.$t('recruitment.mark_interview_failed')},
        {id: 7, label: this.$t('recruitment.offer_job')},
        {id: 8, label: this.$t('recruitment.decline_offer')},
        {id: 9, label: this.$t('recruitment.hire')},
      ],
      disabled: false,
    };
  },

  computed: {
    performedBy() {
      return this.translateEmpName(this.history.performedBy, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    },
    performedDate() {
      return formatDate(
        parseDate(this.history.performedDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
    },
    performedAction() {
      return (
        this.statuses.find((item) => item.id === this.history.action.id)
          ?.label || null
      );
    },
    isScheduleInterview() {
      return this.history.interview?.id && this.history.action?.id === 4;
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.historyId)
      .then((response) => {
        const {data, meta} = response.data;
        this.history = {...data};
        this.disabled = meta.disabled;
        return this.isScheduleInterview
          ? this.http.request({
              method: 'GET',
              url: `/api/v2/recruitment/candidates/${this.candidateId}/interviews/${this.history.interview.id}`,
            })
          : null;
      })
      .then((response) => {
        if (response) {
          const {data} = response.data;
          this.interview.interviewName = data.name;
          this.interview.interviewDate = data.interviewDate;
          this.interview.interviewTime = data.interviewTime;
          this.history.note = data.note;
          if (Array.isArray(data.interviewers)) {
            this.interviewers = data.interviewers.map((interviewer) => ({
              id: interviewer.empNumber,
              label: this.translateEmpName(interviewer, {
                includeMiddle: true,
                excludePastEmpTag: true,
              }),
              isPastEmployee: interviewer.terminationId ? true : false,
            }));
          }
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
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
      this.loading = true;
      this.http
        .update(this.historyId, {
          note: this.history.note,
        })
        .then(() => {
          return this.isScheduleInterview
            ? this.http.request({
                method: 'PUT',
                url: `/api/v2/recruitment/candidates/${this.candidateId}/interviews/${this.history.interview.id}`,
                data: {
                  ...this.interview,
                  note: this.history.note,
                  interviewerEmpNumbers: this.interviewers
                    .map((interviewer) => interviewer?.id)
                    .filter(Number),
                },
              })
            : null;
        })
        .then(() => {
          this.loading = false;
          this.$toast.saveSuccess();
        });
    },
    onClickBack() {
      navigate('/recruitment/addCandidate/{id}', {id: this.candidateId});
    },
  },
};
</script>
