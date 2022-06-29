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
  <div class="orangehrm-background-container">
    <candidate-action-layout
      v-model:loading="isLoading"
      :candidate-id="candidateId"
      :title="$t('recruitment.view_action_history')"
      @submitValid="onSave"
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
      <oxd-form-row>
        <oxd-grid :rows="2" :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="orangehrm-save-candidate-page --span-column-2">
            <oxd-input-field
              v-model="history.notes"
              :label="$t('general.notes')"
              type="textarea"
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
        <submit-button :label="$t('general.save')" />
      </oxd-form-actions>
    </candidate-action-layout>
    <br />
    <interview-attachments
      v-if="history.interview.id"
      :max-file-size="maxFileSize"
      :interview-id="history.interview.id"
      :allowed-file-types="allowedFileTypes"
    ></interview-attachments>
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import InterviewAttachments from '@/orangehrmRecruitmentPlugin/components/InterviewAttachments.vue';
import CandidateActionLayout from '@/orangehrmRecruitmentPlugin/components/CandidateActionLayout.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

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

export default {
  components: {
    'interview-attachments': InterviewAttachments,
    'candidate-action-layout': CandidateActionLayout,
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
    maxFileSize: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },

  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/candidates/${props.candidateId}/history/${props.historyId}`,
    );

    return {
      http,
      locale,
      jsDateFormat,
      translateEmpName: $tEmpName,
    };
  },

  data() {
    return {
      isLoading: false,
      history: {...actionHistoryModel},
      statuses: [
        {id: 1, label: this.$t('recruitment.application_initiated')},
        {id: 2, label: this.$t('recruitment.shortlisted')},
        {id: 3, label: this.$t('recruitment.rejected')},
        {id: 4, label: this.$t('recruitment.interview_scheduled')},
        {id: 5, label: this.$t('recruitment.interview_passed')},
        {id: 6, label: this.$t('recruitment.interview_failed')},
        {id: 7, label: this.$t('recruitment.job_offered')},
        {id: 8, label: this.$t('recruitment.offer_declined')},
        {id: 9, label: this.$t('recruitment.hired')},
      ],
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
        this.statuses.find(item => item.id === this.history.action.id)?.label ||
        null
      );
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.history = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.loading = true;
      this.http
        .update(this.historyId, {
          note: this.action.notes,
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
