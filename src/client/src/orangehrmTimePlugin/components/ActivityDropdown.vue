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
  <oxd-input-field type="select" :options="options"> </oxd-input-field>
</template>

<script>
import {ref, watchEffect} from 'vue';
import {APIService} from '@ohrm/core/util/services/api.service';

export default {
  name: 'ActivityDropdown',
  props: {
    projectId: {
      type: Number,
      required: false,
      default: null,
    },
  },
  setup(props) {
    const options = ref([]);
    const http = new APIService(window.appGlobal.baseUrl, '');

    watchEffect(async () => {
      if (props.projectId) {
        http
          .request({
            method: 'GET',
            url: `/api/v2/time/project/${props.projectId}/activities`,
            params: {limit: 0},
          })
          .then(({data}) => {
            options.value = data.data.map((item) => {
              return {
                id: item.id,
                label: item.name,
                isDeleted: item.deleted,
              };
            });
          });
      } else {
        options.value = [];
      }
    });

    return {
      options,
    };
  },
};
</script>

<style scoped>
::v-deep(.oxd-select-wrapper) {
  min-width: 150px;
}
</style>
