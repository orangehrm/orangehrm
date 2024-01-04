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
    :label="$t('general.status')"
    :options="options"
  />
</template>

<script>
import {ref} from 'vue';
export default {
  name: 'StatusDropdown',
  data() {
    const options = ref([
      {
        id: 'INITIATED',
        label: this.$t('claim.initiated'),
      },
      {
        id: 'SUBMITTED',
        label: this.$t('time.submitted'),
      },
      {
        id: 'APPROVED',
        label: this.$t('time.approved'),
      },
      {
        id: 'REJECTED',
        label: this.$t('leave.rejected'),
      },
      {
        id: 'CANCELLED',
        label: this.$t('leave.cancelled'),
      },
      {
        id: 'PAID',
        label: this.$t('claim.paid'),
      },
    ]);
    return {
      options,
    };
  },
};
</script>
