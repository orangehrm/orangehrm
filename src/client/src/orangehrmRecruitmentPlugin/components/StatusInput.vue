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
    :label="$t('recruitment.current_status')"
    disabled
    :value="getStatus"
  />
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
export default {
  name: 'StatusInput',
  props: {
    statusId: {
      type: String,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      'https://0d188518-fc5f-4b13-833d-5cd0e9fcef79.mock.pstmn.io',
      'recruitment/statuses',
    );
    return {
      http,
    };
  },
  data() {
    return {
      statuses: null,
    };
  },
  computed: {
    getStatus() {
      return (
        this.statuses?.find(({status}) => status == this.statusId)?.label ||
        null
      );
    },
  },
  beforeMount() {
    this.http.getAll().then(({data: {data}}) => {
      this.statuses = data;
    });
  },
};
</script>

<style scoped></style>
