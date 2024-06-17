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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.select_employee') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form @submit-valid="viewTimesheet">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="employee"
                :rules="rules.employee"
                :params="{
                  includeEmployees: 'currentAndPast',
                }"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <submit-button :label="$t('general.view')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
    <br />

    <timesheet-pending-actions></timesheet-pending-actions>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
  validSelection,
} from '@/core/util/validation/rules';
import {navigate} from '@ohrm/core/util/helper/navigation';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import TimesheetPendingActions from '@/orangehrmTimePlugin/components/TimesheetPendingActions.vue';

export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'timesheet-pending-actions': TimesheetPendingActions,
  },

  data() {
    return {
      employee: null,
      rules: {
        employee: [required, shouldNotExceedCharLength(100), validSelection],
      },
    };
  },

  methods: {
    viewTimesheet() {
      navigate('/time/viewTimesheet/employeeId/{id}', {id: this.employee?.id});
    },
  },
};
</script>
