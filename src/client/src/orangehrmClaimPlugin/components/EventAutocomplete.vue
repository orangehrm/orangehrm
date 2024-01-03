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
    :clear="false"
    :label="$t('claim.event_name')"
    :create-options="loadEvents"
  >
    <template #option="{data}">
      <span> {{ data.label }} </span>
    </template>
  </oxd-input-field>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
export default {
  name: 'EventAutocomplete',
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/claim/events',
    );
    return {
      http,
    };
  },
  methods: {
    async loadEvents(serachParam) {
      return new Promise((resolve) => {
        if (serachParam.trim()) {
          const params = {
            name: serachParam.trim(),
          };
          this.http.getAll(params).then(({data}) => {
            resolve(
              data.data.map((event) => {
                return {
                  id: event.id,
                  label: event.name,
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
span {
  word-break: break-word;
}
</style>
