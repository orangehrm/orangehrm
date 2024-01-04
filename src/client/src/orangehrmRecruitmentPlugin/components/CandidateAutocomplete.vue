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
  <oxd-input-field
    type="autocomplete"
    :label="$t('recruitment.candidate_name')"
    :clear="false"
    :create-options="loadCandidates"
  >
    <template #option="{data}">
      <span>{{ data.label }}</span>
    </template>
  </oxd-input-field>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
export default {
  name: 'CandidateAutocomplete',
  props: {
    params: {
      type: Object,
      default: () => ({}),
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
  methods: {
    async loadCandidates(serachParam) {
      return new Promise((resolve) => {
        if (serachParam.trim()) {
          this.http
            .getAll({
              candidateName: serachParam.trim(),
              ...this.params,
            })
            .then(({data}) => {
              resolve(
                data.data.map((candidate) => {
                  return {
                    id: candidate.id,
                    label: `${candidate.firstName} ${
                      candidate.middleName || ''
                    } ${candidate.lastName}`,
                    _candidate: candidate,
                  };
                }),
              );
            });
        } else {
          resolve([]);
        }
      });
    },
  },
};
</script>

<style scoped>
.past-employee-tag {
  margin-left: auto;
}
</style>
