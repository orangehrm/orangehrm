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
    <oxd-table-filter :filter-title="$t('general.directory')">
      <oxd-form>
        <oxd-form-row>
          <oxd-grid :cols="3">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="filters.employeeNumber"
                api-path="api/v2/directory/employees"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.jobTitleId"
                type="select"
                :label="$t('general.job_title')"
                :options="jobTitles"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.locationId"
                :label="$t('general.location')"
                :options="locations"
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
            @click="triggerReset"
          />
          <oxd-button
            :label="$t('general.search')"
            class="orangehrm-left-space"
            display-type="secondary"
            type="submit"
            @click="triggerSearch"
          />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>
    <br />
    <div :class="{'orangehrm-corporate-directory': isEmployeeSelected}">
      <div class="orangehrm-paper-container orangehrm-full-width">
        <table-header
          :selected="0"
          :total="total"
          :loading="isLoading"
          :show-divider="false"
        ></table-header>
        <oxd-grid ref="scrollerRef" :cols="colSize" :class="oxdGridClasses">
          <oxd-grid-item v-for="(employee, index) in employees" :key="employee">
            <summary-card
              :employee-designation="employee.employeeJobTitle"
              :employee-id="employee.id"
              :employee-location="employee.employeeLocation"
              :employee-name="employee.employeeName"
              :employee-sub-unit="employee.employeeSubUnit"
              :employee-on-mobile="notOnMobile"
              @click="showEmployeeDetails(index)"
            >
              <employee-details
                v-if="isMobile && currentIndex === index"
                :employee-id="employee.id"
                :is-mobile="isMobile"
              >
              </employee-details>
            </summary-card>
          </oxd-grid-item>
          <oxd-loading-spinner
            v-if="isLoading"
            class="orangehrm-container-loader"
          />
        </oxd-grid>
        <div class="orangehrm-bottom-container"></div>
      </div>
      <div
        v-if="isEmployeeSelected && isMobile === false"
        class="orangehrm-paper-container orangehrm-corporate-directory-sidebar"
      >
        <oxd-grid-item>
          <summary-card-details
            :employee-designation="employees[currentIndex].employeeJobTitle"
            :employee-id="employees[currentIndex].id"
            :employee-location="employees[currentIndex].employeeLocation"
            :employee-name="employees[currentIndex].employeeName"
            :employee-sub-unit="employees[currentIndex].employeeSubUnit"
            @hideDetails="hideEmployeeDetails()"
          ></summary-card-details>
        </oxd-grid-item>
      </div>
    </div>
  </div>
</template>
<script>
import {reactive, toRefs} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import useResponsive from '@ohrm/oxd/composables/useResponsive';
import useInfiniteScroll from '@ohrm/core/util/composable/useInfiniteScroll';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import SummaryCard from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCard';
import EmployeeDetails from '@/orangehrmCorporateDirectoryPlugin/components/EmployeeDetails';
import SummaryCardDetails from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCardDetails';

const defaultFilters = {
  employeeNumber: null,
  jobTitleId: null,
  locationId: null,
};

export default {
  name: 'CorporateDirectory',

  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'summary-card': SummaryCard,
    'summary-card-details': SummaryCardDetails,
    'employee-details': EmployeeDetails,
    'oxd-loading-spinner': Spinner,
  },

  props: {
    jobTitles: {
      type: Array,
      default: () => [],
    },
    locations: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const {$t} = usei18n();
    const {noRecordsFound} = useToast();
    const responsiveState = useResponsive();

    const employeeDataNormalizer = data => {
      return data.map(item => {
        return {
          id: item.empNumber,
          employeeName:
            `${item.firstName} ${item.middleName} ${item.lastName} ` +
            (item.terminationId ? $t('general.past_employee') : ''),
          employeeJobTitle: item.jobTitle?.isDeleted
            ? `${item.jobTitle?.title} (${$t('general.deleted')})`
            : item.jobTitle?.title,
          employeeSubUnit: item.subunit?.name,
          employeeLocation: item.location?.name,
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/directory/employees',
    );

    const limit = 8; // this is a static limit since no pagination
    const state = reactive({
      total: 0,
      offset: 0,
      employees: [],
      currentIndex: -1,
      notOnMobile: true,
      isLoading: false,
      filters: {
        ...defaultFilters,
      },
    });

    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: limit,
          offset: state.offset,
          locationId: state.filters.locationId?.id,
          empNumber: state.filters.employeeNumber?.id,
          jobTitleId: state.filters.jobTitleId?.id,
        })
        .then(response => {
          const {data, meta} = response.data;
          state.total = meta?.total || 0;
          if (Array.isArray(data)) {
            state.employees = [
              ...state.employees,
              ...employeeDataNormalizer(data),
            ];
          }
          if (state.total === 0) {
            noRecordsFound();
          }
        })
        .finally(() => (state.isLoading = false));
    };

    const {scrollerRef} = useInfiniteScroll(() => {
      if (state.employees.length >= state.total) return;
      state.offset += limit;
      fetchData();
    });

    return {
      fetchData,
      scrollerRef,
      ...toRefs(state),
      ...toRefs(responsiveState),
    };
  },

  computed: {
    isMobile() {
      return this.windowWidth < 600;
    },
    isEmployeeSelected() {
      return this.currentIndex >= 0;
    },
    oxdGridClasses() {
      return {
        'orangehrm-container': true,
        'orangehrm-container-min-display': this.isEmployeeSelected,
      };
    },
    colSize() {
      if (this.windowWidth >= 1920) {
        return this.isEmployeeSelected ? 5 : 7;
      }
      return this.isEmployeeSelected ? 3 : 4;
    },
  },
  watch: {
    isMobile() {
      this.notOnMobile = !this.isMobile;
    },
  },
  beforeMount() {
    this.fetchData();
  },

  methods: {
    hideEmployeeDetails() {
      this.currentIndex = -1;
      this.notOnMobile = true;
    },
    showEmployeeDetails(index) {
      if (this.currentIndex != index) {
        this.currentIndex = index;
        if (this.isMobile) {
          this.notOnMobile = false;
        }
      } else {
        this.hideEmployeeDetails();
      }
    },
    triggerSearch() {
      this.hideEmployeeDetails();
      this.employees = [];
      this.offset = 0;
      this.fetchData();
    },
    triggerReset() {
      this.hideEmployeeDetails();
      this.employees = [];
      this.filters.employeeNumber = null;
      this.filters.jobTitleId = null;
      this.filters.locationId = null;
      this.fetchData();
    },
  },
};
</script>

<style src="./corporate-directory.scss" lang="scss" scoped></style>
