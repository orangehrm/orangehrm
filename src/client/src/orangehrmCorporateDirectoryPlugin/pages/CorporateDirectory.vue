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
              <subunit-dropdown />
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
    <div class="orangehrm-corporate-directory">
      <div class="orangehrm-paper-container">
        <table-header :loading="isLoading" :show-divider="false" :total="total">
        </table-header>
        <oxd-grid :cols="colSize" class="orangehrm-container">
          <oxd-grid-item v-for="employee in employees" :key="employee">
            <summary-card
              :id="employee.id"
              :employee-designation="employee.employeeJobTitle"
              :employee-location="employee.employeeLocation"
              :employee-name="employee.employeeName"
              :employee-sub-unit="employee.employeeSubUnit"
              @click="
                isMobile === false ? showEmployeeDetailsFn(employee.id) : ''
              "
            ></summary-card>
          </oxd-grid-item>
        </oxd-grid>
        <div class="orangehrm-bottom-container"></div>
      </div>
      <div
        v-if="showDetails && isMobile === false"
        class="orangehrm-paper-container orangehrm-corporate-directory-sidebar"
      >
        <oxd-grid-item>
          <summary-card-details
            :id="employeeDetails[0].id"
            :employee-designation="employeeDetails[0].employeeJobTitle"
            :employee-location="employeeDetails[0].employeeLocation"
            :employee-name="employeeDetails[0].employeeName"
            :employee-sub-unit="employeeDetails[0].employeeSubUnit"
            :employee-work-email="employeeDetails[0].employeeWorkEmail"
            :employee-work-telephone="employeeDetails[0].employeeWorkTelephone"
            @hideDetails="hideEmployeeDetails($event)"
          ></summary-card-details>
        </oxd-grid-item>
      </div>
    </div>
  </div>
</template>
<script>
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown';
import SummaryCard from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCard';
import SummaryCardDetails from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCardDetails';
import {APIService} from '@/core/util/services/api.service';

const employeeDataNormalizer = data => {
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

const employeeDetailsDataNormalizer = data => {
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
      employeeWorkTelephone: item.contactInfo?.workTelephone,
      employeeWorkEmail: item.contactInfo?.workEmail,
    };
  });
};

export default {
  name: 'CorporateDirectory',

  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'jobtitle-dropdown': JobtitleDropdown,
    'subunit-dropdown': SubunitDropdown,
    'summary-card': SummaryCard,
    'summary-card-details': SummaryCardDetails,
  },

  setup() {
    const http = new APIService(
      'https://07bd2c2f-bd2b-4a9f-97c7-cb744a96e0f8.mock.pstmn.io',
      'api/v2/corporate-directory/employees',
    );
    return {
      http,
    };
  },
  data() {
    return {
      employees: [
        {
          id: null,
          employeeName: null,
          employeeJobTitle: null,
          employeeLocation: null,
          employeeSubUnit: null,
        },
      ],
      employeeDetails: [
        {
          id: null,
          employeeName: null,
          employeeJobTitle: null,
          employeeLocation: null,
          employeeSubUnit: null,
          employeeWorkTelephone: null,
          employeeWorkEmail: null,
        },
      ],
      total: null,
      showDetails: false,
      colSize: 4,
      windowWidth: 0,
    };
  },

  computed: {
    isMobile() {
      return this.windowWidth < 600 ? true : false;
    },
  },

  beforeMount() {
    this.http.getAll().then(response => {
      this.employees = employeeDataNormalizer(response.data.data);
      this.total = response.data.meta.total;
    });

    this.onResize();
    window.addEventListener('resize', this.onResize);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.onResize);
  },

  methods: {
    hideEmployeeDetails(event) {
      this.showDetails = event;
      this.colSize = 4;
    },
    showEmployeeDetailsFn(id) {
      this.http.get(id).then(response => {
        this.employeeDetails = employeeDetailsDataNormalizer(
          response.data.data,
        );
        this.showDetails = true;
        this.colSize = 3;
      });
    },
    onResize() {
      this.windowWidth = window.innerWidth;
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-corporate-directory {
  display: flex;

  &-sidebar {
    margin-left: 16px;
  }
}

@media (max-width: 600px) {
  .orangehrm-corporate-directory {
    display: block;
  }
}
</style>
