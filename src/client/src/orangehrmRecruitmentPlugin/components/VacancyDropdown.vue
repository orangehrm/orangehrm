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
    :label="$t('recruitment.vacancy')"
    :options="options"
  />
</template>

<script>
import {ref, onBeforeMount} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'VacancyDropdown',
  props: {
    status: {
      type: Boolean,
      required: false,
      default: null,
    },
    excludeInterviewers: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  setup(props) {
    const options = ref([]);
    const {$t} = usei18n();
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/recruitment/vacancies',
    );
    onBeforeMount(() => {
      const params = {model: 'summary', limit: 0};
      if (props.status !== null) {
        params.status = props.status;
      }
      params.excludeInterviewers = props.excludeInterviewers;
      http.getAll(params).then(({data}) => {
        options.value = data.data.map((item) => {
          return {
            id: item.id,
            label:
              item.status === false
                ? `${item.name} (${$t('general.closed')})`
                : item.name,
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
