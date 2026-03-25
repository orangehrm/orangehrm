<template>
  <reports-table
    module="time"
    name="client"
    :filters="serializedFilters"
    :column-count="4"
  >
    <template #default="{generateReport}">
      <oxd-table-filter filter-title="Client Report">
        <oxd-form @submit-valid="() => handleSubmit(generateReport)">
          <!-- Customer -->
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <customer-autocomplete
                  v-model="filters.customer"
                  label="Customer *"
                  :rules="rules.customer"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <!-- Project + Employee -->
          <oxd-form-row>
            <oxd-grid :cols="2" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <client-project-autocomplete
                  v-model="filters.project"
                  label="Project Name"
                  :customer-id="filters.customer?.id"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <employee-autocomplete
                  v-model="filters.employee"
                  label="Employee Name"
                  :customer-id="filters.customer?.id"
                />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>

          <!-- Date Range + Approved Switch -->
          <oxd-form-row>
            <oxd-grid :cols="4" class="orangehrm-full-width-grid">
              <oxd-grid-item>
                <date-input
                  v-model="filters.fromDate"
                  label="Project Date Range"
                  placeholder="From"
                  :rules="rules.fromDate"
                />
              </oxd-grid-item>

              <oxd-grid-item>
                <date-input
                  v-model="filters.toDate"
                  label="&nbsp;"
                  placeholder="To"
                  :rules="rules.toDate"
                />
              </oxd-grid-item>

              <oxd-grid-item class="orangehrm-switch-filter --span-column-2">
                <oxd-text class="orangehrm-switch-filter-text" tag="p">
                  {{ $t('time.only_include_approved_timesheets') }}
                </oxd-text>
                <oxd-switch-input v-model="filters.timesheetState" />
              </oxd-grid-item>
            </oxd-grid>
          </oxd-form-row>
          <oxd-form-actions>
            <required-text />

            <oxd-button
              type="submit"
              display-type="secondary"
              label="View"
              @click="isDownload = false"
            />

            <oxd-button
              type="submit"
              display-type="secondary"
              label="Download Excel"
              style="margin-left: 20px"
              @click="isDownload = true"
            />
          </oxd-form-actions>
        </oxd-form>
      </oxd-table-filter>

      <br />
    </template>

    <!-- Footer -->
    <template #footer="{data}">
      Total Hours Worked:
      {{ data.meta ? data.meta.sum.label : '0.00' }}
    </template>
  </reports-table>
</template>

<script>
import {computed, ref, watch} from 'vue';
import {OxdSwitchInput} from '@ohrm/oxd';

import {
  validDateFormat,
  endDateShouldBeAfterStartDate,
  startDateShouldBeBeforeEndDate,
} from '@/core/util/validation/rules';

import ReportsTable from '@/core/components/table/ReportsTable';
import ClientEmployeeAutocomplete from '@/orangehrmTimePlugin/components/ClientEmployeeAutocomplete.vue';
import CustomerAutocomplete from '@/orangehrmTimePlugin/components/CustomerAutocomplete.vue';
import useDateFormat from '@/core/util/composable/useDateFormat';
import ClientProjectAutocomplete from '@/orangehrmTimePlugin/components/ClientProjectAutocomplete.vue';

const defaultFilters = {
  customer: null,
  project: null,
  employee: null,
  fromDate: null,
  toDate: null,
  timesheetState: false,
};

export default {
  components: {
    'reports-table': ReportsTable,
    'employee-autocomplete': ClientEmployeeAutocomplete,
    'customer-autocomplete': CustomerAutocomplete,
    'oxd-switch-input': OxdSwitchInput,
    'client-project-autocomplete': ClientProjectAutocomplete,
  },

  setup() {
    const filters = ref({...defaultFilters});

    watch(
      () => filters.value.customer,
      () => {
        filters.value.employee = null;
        filters.value.project = null;
      },
    );

    const {userDateFormat} = useDateFormat();

    const isDownload = ref(false);

    const rules = {
      customer: [(v) => !!v || 'Customer is required'],
      employee: [
        (v) => {
          if (v && !filters.value.customer) {
            return 'Customer is required';
          }
          return true;
        },
      ],

      project: [
        (v) => {
          if (v && !filters.value.customer) {
            return 'Customer is required';
          }
          return true;
        },
      ],

      fromDate: [
        validDateFormat(userDateFormat),
        startDateShouldBeBeforeEndDate(
          () => filters.value.toDate,
          'From date should be before To date',
          {allowSameDate: true},
        ),
      ],

      toDate: [
        validDateFormat(userDateFormat),
        endDateShouldBeAfterStartDate(
          () => filters.value.fromDate,
          'To date should be after From date',
          {allowSameDate: true},
        ),
      ],
    };

    const serializedFilters = computed(() => {
      return {
        customerId: filters.value.customer?.id,
        projectId: filters.value.project?.id,
        empNumber: filters.value.employee?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        timesheetState: filters.value.timesheetState ? 'onlyApproved' : 'all',
      };
    });

    const handleSubmit = (generateReport) => {
      if (isDownload.value) {
        const params = new URLSearchParams({
          customerId: filters.value.customer?.id,
          projectId: filters.value.project?.id,
          empNumber: filters.value.employee?.id,
          fromDate: filters.value.fromDate,
          toDate: filters.value.toDate,
          timesheetState: filters.value.timesheetState ? 'onlyApproved' : 'all',
        }).toString();

        window.open(
          `/cothrm/web/index.php/time/client-report/excel?${params}`,
          '_blank',
        );
        isDownload.value = false;
      } else {
        generateReport();
      }
    };

    return {
      filters,
      rules,
      serializedFilters,
      handleSubmit,
      isDownload,
    };
  },
};
</script>

<style src="./time-reports.scss" lang="scss" scoped></style>
