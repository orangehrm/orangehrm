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
    :label="$t('leave.leave_period')"
    :options="options"
    :model-value="selectedPeriod"
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script>
import {ref, onBeforeMount, computed} from 'vue';
import {APIService} from '@ohrm/core/util/services/api.service';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  name: 'LeavePeriodDropdown',
  props: {
    modelValue: {
      type: Object,
      default: null,
    },
  },
  emits: ['update:modelValue'],
  setup(props) {
    const options = ref([]);
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/leave-periods',
    );
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    onBeforeMount(() => {
      http.getAll().then(({data}) => {
        options.value = data.data.map((item) => {
          const startDate = formatDate(
            parseDate(item.startDate),
            jsDateFormat,
            {locale},
          );
          const endDate = formatDate(parseDate(item.endDate), jsDateFormat, {
            locale,
          });

          return {
            id: `${item.startDate}_${item.endDate}`,
            label: `${startDate} - ${endDate}`,
            startDate: item.startDate,
            endDate: item.endDate,
          };
        });
      });
    });

    const selectedPeriod = computed(() => {
      return options.value.find(
        (_option) => _option.id === props.modelValue?.id,
      );
    });

    return {
      options,
      selectedPeriod,
    };
  },
};
</script>
