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
  <oxd-input-field
    type="date"
    placeholder="yyyy-mm-dd"
    :day-attributes="attributes"
    :events="events"
    @selectYear="onSelectYear"
  />
</template>

<script>
import {onBeforeMount, reactive, toRefs} from 'vue';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  startOfYear,
  endOfYear,
  formatDate,
  parseDate,
} from '@ohrm/core/util/helper/datefns';

export default {
  name: 'DateInput',
  setup() {
    const state = reactive({
      attributes: [],
      events: [],
    });
    const http = new APIService(window.appGlobal.baseUrl, '');

    const fetchWorkWeek = async () => {
      http
        .request({
          type: 'GET',
          url: 'api/v2/leave/workweek',
          params: {
            model: 'indexed',
          },
        })
        .then(({data}) => {
          if (data?.data) {
            state.attributes = Object.keys(data.data).map(i => {
              return {
                index: parseInt(i),
                class:
                  data.data[i] === 8
                    ? '--non-working-day'
                    : data.data[i] === 4
                    ? '--working-day-half'
                    : '',
              };
            });
          }
        });
    };

    const fetchEvents = async (fromDate, toDate) => {
      http
        .request({
          type: 'GET',
          url: 'api/v2/leave/holidays',
          params: {
            fromDate,
            toDate,
          },
        })
        .then(({data}) => {
          if (Array.isArray(data?.data)) {
            state.events = data.data.map(event => {
              return {
                date: parseDate(event.date, 'yyyy-MM-dd'),
                type: event.name,
                class: event.length === 0 ? '--holiday-full' : '--holiday-half',
              };
            });
          }
        });
    };

    const onSelectYear = async ({year}) => {
      const now = new Date().setFullYear(year);
      const fromDate = formatDate(startOfYear(now), 'yyyy-MM-dd');
      const endDate = formatDate(endOfYear(now), 'yyyy-MM-dd');
      fetchEvents(fromDate, endDate);
    };

    onBeforeMount(fetchWorkWeek);
    onBeforeMount(onSelectYear({year: new Date().getFullYear()}));

    return {
      ...toRefs(state),
      onSelectYear,
    };
  },
};
</script>
