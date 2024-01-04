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
    type="select"
    label="Current OrangeHRM Version"
    :options="options"
  />
</template>

<script>
import {ref, onBeforeMount} from 'vue';
import {APIService} from '@/core/util/services/api.service';
export default {
  name: 'VersionDropdown',
  setup() {
    const options = ref([]);
    const http = new APIService(
      window.appGlobal.baseUrl,
      'upgrader/api/versions',
    );
    onBeforeMount(() => {
      http.getAll().then(({data}) => {
        options.value = data.map((item) => {
          return {
            id: item,
            label: item,
          };
        });
      });
    });
    return {
      options,
    };
  },
};
</script>
