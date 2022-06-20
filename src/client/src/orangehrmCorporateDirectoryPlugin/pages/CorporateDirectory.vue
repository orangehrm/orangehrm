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
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
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
    <div :class="{'orangehrm-corporate-directory': hasCurrentIndex}">
      <div class="orangehrm-paper-container">
        <table-header :show-divider="false" :total="total"></table-header>
        <oxd-grid ref="scrollerRef" :cols="colSize" :class="oxdGridClasses">
          <oxd-grid-item v-for="(employee, index) in employees" :key="employee">
            <summary-card
              :employee-designation="employee.employeeJobTitle"
              :employee-id="employee.id"
              :employee-location="employee.employeeLocation"
              :employee-name="employee.employeeName"
              :employee-sub-unit="employee.employeeSubUnit"
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
        v-if="currentIndex > -1 && isMobile === false"
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
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import SummaryCard from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCard';
import SummaryCardDetails from '@/orangehrmCorporateDirectoryPlugin/components/SummaryCardDetails';
import EmployeeDetails from '@/orangehrmCorporateDirectoryPlugin/components/EmployeeDetails';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import useInfiniteScroll from '@ohrm/core/util/composable/useInfiniteScroll';
import {reactive, toRefs} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import {ref} from 'vue';
import useToast from '@/core/util/composable/useToast';

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
    const {noRecordsFound} = useToast();
    const {$t} = usei18n();
    const employeeDataNormalizer = data => {
      return data.map(item => {
        return {
          id: item.empNumber,
          employeeName:
            `${item.firstName} ${item.middleName} ${item.lastName} ` +
            (item.terminationId ? $t('general.past_employee') : ''),
          employeeJobTitle: item.jobTitle?.isDeleted
            ? `${item.jobTitle?.title} ` + $t('general.deleted')
            : item.jobTitle?.title,
          employeeSubUnit: item.subunit?.name,
          employeeLocation: item.location?.name,
        };
      });
    };
    const filters = ref({...defaultFilters});
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/directory/employees',
    );
    const limit = 8; // this is a static limit since no pagination
    const state = reactive({
      employees: [],
      total: 0,
      colSize: 4,
      windowWidth: 0,
      currentIndex: -1,
      isLoading: false,
      offset: 0,
    });

    const fetchData = () => {
      state.isLoading = true;
      http
        .getAll({
          limit: limit,
          offset: state.offset,
          locationId: filters.value.locationId?.id,
          empNumber: filters.value.employeeNumber?.id,
          jobTitleId: filters.value.jobTitleId?.id,
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
      scrollerRef,
      ...toRefs(state),
      fetchData,
      filters,
    };
  },

  computed: {
    isMobile() {
      return this.windowWidth < 600;
    },
    hasCurrentIndex() {
      return this.currentIndex >= 0;
    },
    oxdGridClasses() {
      return {
        'orangehrm-container': true,
        'orangehrm-container-min-display': this.hasCurrentIndex,
      };
    },
  },

  beforeMount() {
    this.fetchData();

    this.onResize();
    window.addEventListener('resize', this.onResize);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.onResize);
  },
  methods: {
    hideEmployeeDetails() {
      this.currentIndex = -1;
      this.colSize = 4;
    },
    showEmployeeDetails(index) {
      if (this.currentIndex != index) {
        this.currentIndex = index;
        this.colSize = 3;
      } else {
        this.hideEmployeeDetails();
      }
    },
    onResize() {
      this.windowWidth = window.innerWidth;
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

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-corporate-directory {
  display: block;
  @include oxd-respond-to('md') {
    display: flex;
    justify-content: space-between;
  }

  &-sidebar {
    margin-left: 16px;
  }
}

.orangehrm-container {
  overflow: auto;
  height: 512px;
  position: relative;
  margin: 0px;
  @include oxd-respond-to('md') {
    min-width: 678px;
  }
  &-min-display {
    min-width: auto;
  }

  @include oxd-scrollbar();

  &-loader {
    margin: 0 auto;
    background-color: $oxd-white-color;
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    bottom: 0;
  }
}
.oxd-grid-item {
  padding: 0.5rem 0.75rem;
}
</style>
