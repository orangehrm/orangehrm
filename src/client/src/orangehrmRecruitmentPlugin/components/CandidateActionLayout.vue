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
  <div class="orangehrm-card-container">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ title }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="loading" v-bind="$attrs">
      <oxd-form-row>
        <oxd-grid :cols="3">
          <oxd-grid-item>
            <oxd-input-field
              v-model="candidate.candidateName"
              :label="$t('recruitment.candidate')"
              readonly
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <oxd-input-field
              v-model="candidate.vacancyName"
              :label="$t('recruitment.vacancy')"
              readonly
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <oxd-input-field
              v-model="candidate.hiringManagerName"
              :label="$t('recruitment.hiring_manager')"
              readonly
            />
          </oxd-grid-item>
          <oxd-grid-item class="--offset-row-2">
            <oxd-input-field
              v-model="recruitmentStatus"
              :label="$t('recruitment.current_status')"
              readonly
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <slot></slot>
    </oxd-form>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

const candidateModel = {
  candidateName: '',
  vacancyName: '',
  hiringManagerName: '',
  status: null,
};

export default {
  name: 'CandidateActionLayout',
  inheritAttrs: false,
  props: {
    candidateId: {
      type: Number,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['update:loading'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/candidates`,
    );
    const {$tEmpName} = useEmployeeNameTranslate();

    return {
      http,
      translateEmpName: $tEmpName,
    };
  },
  data() {
    return {
      candidate: {...candidateModel},
      statuses: [
        {id: 1, label: this.$t('recruitment.application_initiated')},
        {id: 2, label: this.$t('recruitment.shortlisted')},
        {id: 3, label: this.$t('leave.rejected')},
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
    recruitmentStatus() {
      return (
        this.statuses.find((item) => item.id === this.candidate.status?.id)
          ?.label || null
      );
    },
  },
  beforeMount() {
    this.$emit('update:loading', true);
    this.http
      .get(this.candidateId)
      .then((response) => {
        const {data} = response.data;
        this.candidate.status = data.status;
        this.candidate.candidateName = `${data?.firstName} ${
          data?.middleName || ''
        } ${data?.lastName}`;
        if (data?.vacancy) {
          this.candidate.vacancyName = data?.vacancy.name;
        }
        if (data?.vacancy?.hiringManager) {
          this.candidate.hiringManagerName = this.translateEmpName(
            data.vacancy.hiringManager,
            {
              includeMiddle: true,
              excludePastEmpTag: false,
            },
          );
        }
      })
      .finally(() => {
        this.$emit('update:loading', false);
      });
  },
};
</script>
