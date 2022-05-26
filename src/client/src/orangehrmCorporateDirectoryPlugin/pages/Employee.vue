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
  <div class="orangehrm-background-container">
    <oxd-table-filter :filter-title="$t('Directory')">
      <oxd-form>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete />
            </oxd-grid-item>
            <oxd-grid-item>
              <jobtitle-dropdown />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :clear="false"
                :label="$t('general.location')"
                :options="countries"
                type="select"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            :label="$t('general.reset')"
            display-type="ghost"
            type="reset"
          />
          <oxd-button
            :label="$t('general.search')"
            class="orangehrm-left-space"
            display-type="secondary"
            type="submit"
          />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>
    <br />
    <div class="orangehrm-paper">
      <div class="orangehrm-paper-container">
        <table-header
          :loading="isLoading"
          :show-divider="false"
          total="10"
        ></table-header>
        <oxd-grid :cols="4" class="orangehrm-container">
          <oxd-grid-item v-for="employee in employees" :key="employee">
            <summary-card
              :id="employee.id"
              :employee-designation="employee.employeeJobTitle"
              :employee-location="employee.employeeLocation"
              :employee-name="employee.employeeName"
              :employee-sub-unit="employee.employeeSubUnit"
            ></summary-card>
          </oxd-grid-item>
        </oxd-grid>
        <div class="orangehrm-bottom-container"></div>
      </div>
      <div class="orangehrm-paper-container orangehrm-paper-container-details">
        <oxd-grid :cols="1">
          <oxd-grid-item>
            <summary-card-details
              :id="employees[0].id"
              :employee-designation="employees[0].employeeJobTitle"
              :employee-location="employees[0].employeeLocation"
              :employee-name="employees[0].employeeName"
              :employee-sub-unit="employees[0].employeeSubUnit"
            ></summary-card-details>
          </oxd-grid-item>
        </oxd-grid>
        <div class="orangehrm-bottom-container"></div>
      </div>
    </div>
  </div>
</template>
<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import SummaryCard from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCard';
import SummaryCardDetails from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCardDetails';
import {APIService} from '@/core/util/services/api.service';

const userdataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      employeeId: item.employeeId,
      employeeName:
        `${item.firstName} ${item.middleName} ${item.lastName}` +
        (item.terminationId ? ' (Past Employee)' : ''),
      employeeJobTitle: item.jobTitle?.title,
      employeeSubUnit: item.subunit?.name,
      employeeLocation: item.location?.name,
    };
  });
};

export default {
  name: 'Employee',

  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
    'summary-card': SummaryCard,
    'summary-card-details': SummaryCardDetails,
  },

  props: {
    countries: {
      type: Array,
      default: () => [],
    },
  },
  setup() {
    const http = new APIService(
      'https://4f792798-fc9b-4ba7-b530-f20c22eb65f0.mock.pstmn.io',
      'api/v2/corporate-directory/employees',
    );

    return {
      http,
    };
  },
  data() {
    return {
      checkedItems: [],
      info: this.http,
      employees: [
        {
          id: 0,
          employeeName: '',
          employeeJobTitle: '',
          employeeLocation: '',
          employeeSubUnit: '',
        },
      ],
    };
  },
  beforeMount() {
    this.http.getAll().then(response => {
      this.employees = userdataNormalizer(response.data.data);
    });
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-paper {
  display: flex;

  &-container-details {
    margin-left: 10px;
  }
}
</style>
