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
  <recruitment-status :candidate-id="candidateId"> </recruitment-status>
  <candidate-profile
    :candidate-id="candidateId"
    :max-file-size="maxFileSize"
    :allowed-file-types="allowedFileTypes"
  ></candidate-profile>
  <history-table :candidate-id="candidateId"></history-table>
</template>

<script>
import HistoryTable from '@/orangehrmRecruitmentPlugin/components/HistoryTable';
import CandidateProfile from '@/orangehrmRecruitmentPlugin/components/CandidateProfile';
import RecruitmentStatus from '@/orangehrmRecruitmentPlugin/components/RecruitmentStatus';

export default {
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
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },
};
</script>
