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
  <candidate-profile-layout :candidate-id="candidateId" :action="action">
    <template #form-footer>
      <div class="orangehrm-form-footer">
        <oxd-text type="subtitle-2" class="orangehrm-status-title">
          {{ $t('general.status') }}: {{ action.label }}
        </oxd-text>
        <oxd-form-actions class="orangehrm-form-buttons">
          <oxd-button
            display-type="ghost-danger"
            :label="$t('recruitment.reject')"
            @click="onReject"
          />
          <oxd-button
            display-type="ghost-danger"
            :label="$t('recruitment.mark_interview_failed')"
            @click="onFailed"
          />
          <oxd-button
            display-type="secondary"
            :label="$t('recruitment.mark_interview_passed')"
            @click="onPassed"
          />
        </oxd-form-actions>
      </div>
    </template>
  </candidate-profile-layout>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import CandidateProfileLayout from '@/orangehrmRecruitmentPlugin/components/CandidateProfileLayout';
export default {
  name: 'InterviewScheduleAction',
  components: {
    'candidate-profile-layout': CandidateProfileLayout,
  },
  props: {
    candidateId: {
      type: Number,
      required: true,
    },
    action: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      data: null,
      status: null,
      isLoading: true,
      vacancyId: null,
    };
  },
  methods: {
    getData(data) {
      this.data = data.stage;
      this.vacancyId = data.vacancyId;
      this.isLoading = false;
    },
    onReject() {
      navigate('recruitment/vacancy/action');
    },
    onPassed() {
      navigate('recruitment/vacancy/action');
    },
    onFailed() {
      navigate('recruitment/vacancy/action');
    },
  },
};
</script>
<style scoped lang="scss" src="./candidate-profile.scss"></style>
