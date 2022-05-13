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
  <oxd-form :loading="isLoading">
    <recruitment-status :data="data">
      <template #header-title>
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('recruitment.application_stage') }}
        </oxd-text>
      </template>
      <template #footer-title>
        <oxd-text type="subtitle-2"> Status: {{ action.label }} </oxd-text>
      </template>
      <template #footer-options>
        <oxd-button
          display-type="ghost-danger"
          :label="$t('recruitment.reject')"
          @click="onReject"
        />
        <oxd-button
          display-type="ghost-danger"
          :label="$t('recruitment.decline_offer')"
          @click="onDecline"
        />
        <oxd-button
          display-type="secondary"
          :label="$t('recruitment.hire')"
          @click="onHire"
        />
      </template>
    </recruitment-status>
  </oxd-form>
  <candidate-profile
    :candidate-id="candidateId"
    @getData="getData"
  ></candidate-profile>
  <history-table :candidate-id="candidateId"></history-table>
</template>

<script>
import RecruitmentStatus from '@/orangehrmRecruitmentPlugin/components/RecruitmentStatus';
import CandidateProfile from '@/orangehrmRecruitmentPlugin/components/CandidateProfile';
import HistoryTable from '@/orangehrmRecruitmentPlugin/components/HistoryTable';
import {navigate} from '@/core/util/helper/navigation';
export default {
  name: 'JobOfferedAction',
  components: {
    'history-table': HistoryTable,
    'candidate-profile': CandidateProfile,
    'recruitment-status': RecruitmentStatus,
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
    onDecline() {
      navigate('recruitment/vacancy/action');
    },
    onHire() {
      navigate('recruitment/vacancy/action');
    },
  },
};
</script>

<style scoped lang="scss"></style>
