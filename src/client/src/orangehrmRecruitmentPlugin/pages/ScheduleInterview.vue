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
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('recruitment.schedule_interview') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.candidate')"
                disabled
                :value="schedule.candidate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.vacancy')"
                disabled
                :value="schedule.vacancy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.hiring_manager')"
                disabled
                :value="schedule.hiringManager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.current_status')"
                disabled
                :value="schedule.status"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="schedule.title"
                :label="$t('recruitment.interview_title')"
                :rules="rules.title"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <interviewer-autocomplete
                v-for="(interviewer, index) in interviewers"
                :key="index"
                v-model="interviewer.value"
                :label="$t('recruitment.interviewer')"
                :show-delete="index > 0"
                :rules="
                  index === 0 ? rules.mainInterviewer : rules.subInterviewer
                "
                include-employees="onlyCurrent"
                :required="index === 0"
                @remove="onRemoveAdmin(index)"
              />
              <oxd-button
                v-if="interviewers.length < 5"
                icon-name="plus"
                display-type="text"
                :label="$t('general.add_another')"
                @click="onAddAnother"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="schedule.date"
                :label="$t('general.date')"
                :placeholder="$t('general.date_format')"
                :rules="rules.date"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <time-input v-model="schedule.time" :label="$t('general.time')" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page --span-column-2"
            >
              <oxd-input-field
                v-model="schedule.note"
                :label="$t('general.notes')"
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
            :label="$t('general.back')"
            @click="onBack"
          />
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
  <interview-attachments
    :allowed-file-types="allowedFileTypes"
  ></interview-attachments>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@ohrm/core/util/validation/rules';
import InterviewAttachments from '@/orangehrmRecruitmentPlugin/components/InterviewAttachments';
import InterviewerAutocomplete from '@/orangehrmRecruitmentPlugin/components/InterviewerAutocomplete';

export default {
  name: 'ScheduleInterview',
  components: {
    'interview-attachments': InterviewAttachments,
    'interviewer-autocomplete': InterviewerAutocomplete,
  },
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      'https://0d188518-fc5f-4b13-833d-5cd0e9fcef79.mock.pstmn.io',
      'recruitment/scheduleInterview',
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      schedule: {
        candidate: null,
        vacancy: null,
        hiringManager: null,
        status: null,
        title: '',
        interviewers: null,
        date: null,
        time: null,
        notes: null,
      },

      rules: {
        mainInterviewer: [
          required,
          value => {
            return this.interviewers.filter(
              ({value: interviewer}) =>
                interviewer && interviewer.id === value?.id,
            ).length < 2
              ? true
              : this.$t('general.already_exists');
          },
        ],
        subInterviewer: [
          value => {
            return this.interviewers.filter(
              ({value: interviewer}) =>
                interviewer && interviewer.id === value?.id,
            ).length < 2
              ? true
              : this.$t('general.already_exists');
          },
        ],
        date: [required, validDateFormat()],
        title: [required, shouldNotExceedCharLength(100)],
      },
      interviewers: [{value: null}],
      interviewerId: null,
    };
  },
  beforeMount() {
    this.http.getAll().then(({data: {data}}) => {
      const {vacancy, candidate, manager, status, ...rest} = data;
      this.schedule = {
        cid: candidate.id,
        hid: data.id,
        candidate: `${candidate.firstName} ${candidate.middleName} ${candidate.lastName}`,
        vacancy: vacancy.title,
        hiringManager: `${manager.firstName} ${manager.middleName} ${manager.lastName}`,
        status: status.label,
        ...rest,
      };
    });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          interviewers: this.interviewers.map(({value}) => value.id),
          ...this.schedule,
        })
        .then(result => {
          this.interviewerId = result.data?.data.id;
          return this.$toast.saveSuccess();
        })
        .then(() => {
          navigate(
            `/recruitment/jobInterview/history/${this.schedule.hid}/interviewer/${this.interviewerId}`,
          );
        });
    },
    onBack() {
      navigate(`/recruitment/addCandidate/${this.schedule.cid}`);
    },
    onAddAnother() {
      this.interviewers.push({value: null});
    },
    onRemoveAdmin(index) {
      this.interviewers.splice(index, 1);
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-project-admin-input {
  align-items: center;
}
</style>
