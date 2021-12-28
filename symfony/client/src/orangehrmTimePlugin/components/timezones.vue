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
    type="select"
    :options="options"
    :label="$t('time.timezone')"
  />
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
export default {
  setup() {
    //date punch-in data submiting Api
    const http = new APIService(
      'https://62fc498d-0f01-41d2-b3ed-bd7280ebc66c.mock.pstmn.io',
      '/api/v2/time/timezones',
    );
    return {
      http,
    };
  },

  data() {
    return {
      options: [],
    };
  },
  beforeMount() {
    this.http.getAll().then(res => {
      const {data} = res;
      this.options = data.data.timezones;
    });
  },
};
</script>
