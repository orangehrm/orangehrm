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
  <recruitment-status v-if="candidate" :candidate="candidate">
  </recruitment-status>
  <candidate-profile
    v-if="candidate"
    :candidate="candidate"
    :allowed-file-types="allowedFileTypes"
    :max-file-size="maxFileSize"
    :updatable="updatable"
    @update="onCandidateUpdate"
  ></candidate-profile>
  <history-table v-if="candidate" :candidate="candidate"></history-table>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import RecruitmentStatus from '@/orangehrmRecruitmentPlugin/components/RecruitmentStatus';
import CandidateProfile from '@/orangehrmRecruitmentPlugin/components/CandidateProfile';
import HistoryTable from '@/orangehrmRecruitmentPlugin/components/HistoryTable';

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
    maxFileSize: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    updatable: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/candidates',
    );
    return {
      http,
    };
  },
  data() {
    return {
      candidate: null,
    };
  },
  beforeMount() {
    this.onCandidateUpdate();
  },
  methods: {
    onCandidateUpdate() {
      this.http.get(this.candidateId).then(({data: {data}}) => {
        this.candidate = data;
      });
    },
  },
};
</script>
