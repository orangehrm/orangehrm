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
  <oxd-table-filter
    :filter-title="$t('attendance.employee_attendance_records')"
  >
    <oxd-form @submit-valid="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.employee"
              :rules="rules.employee"
              :params="{
                includeEmployees: 'currentAndPast',
              }"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="filters.date"
              :rules="rules.date"
              :years="yearArray"
              :label="$t('general.date')"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />

      <oxd-form-actions>
        <required-text />
        <oxd-button
          display-type="secondary"
          :label="$t('general.view')"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-table-filter>
  <br />
  <div class="orangehrm-paper-container">
    <table-header
      :total="total"
      :selected="0"
      :loading="isLoading"
      :show-divider="false"
    ></table-header>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items?.data"
        :selectable="false"
        :clickable="false"
        :loading="isLoading"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div class="orangehrm-bottom-container">
      <oxd-pagination
        v-if="showPaginator"
        v-model:current="currentPage"
        :length="pages"
      />
    </div>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@/core/util/validation/rules';
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {freshDate, formatDate} from '@ohrm/core/util/helper/datefns';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import {yearRange} from '@/core/util/helper/year-range';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
  },

  props: {
    date: {
      type: String,
      default: null,
    },
  },

  setup(props) {
    const {$tEmpName} = useEmployeeNameTranslate();
    const {userDateFormat} = useDateFormat();

    const rules = {
      date: [required, validDateFormat(userDateFormat)],
      employee: [shouldNotExceedCharLength(100)],
    };

    const filters = ref({
      date: props.date ? props.date : formatDate(freshDate(), 'yyyy-MM-dd'),
      employee: null,
    });

    const serializedFilters = computed(() => {
      return {
        date: filters.value.date,
        empNumber: filters.value.employee?.id,
      };
    });

    const attendanceRecordNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.empNumber,
          empName: $tEmpName(item, {
            includeMiddle: false,
            excludePastEmpTag: false,
          }),
          duration: item.sum?.label,
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/attendance/employees/summary',
    );
    const {
      total,
      pages,
      response,
      isLoading,
      execQuery,
      currentPage,
      showPaginator,
    } = usePaginate(http, {
      query: serializedFilters,
      normalizer: attendanceRecordNormalizer,
    });

    return {
      http,
      rules,
      total,
      pages,
      filters,
      isLoading,
      execQuery,
      currentPage,
      showPaginator,
      items: response,
    };
  },

  data() {
    return {
      yearArray: [...yearRange()],
      headers: [
        {
          name: 'empName',
          slot: 'title',
          title: this.$t('general.employee_name'),
          style: {flex: '40%'},
        },
        {
          name: 'duration',
          title: this.$t('time.total_duration'),
          style: {flex: '40%'},
        },
        {
          name: 'actions',
          slot: 'footer',
          title: this.$t('general.actions'),
          style: {flex: '20%'},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                label: this.$t('general.view'),
                displayType: 'text',
                size: 'medium',
              },
            },
          },
        },
      ],
    };
  },

  methods: {
    async resetDataTable() {
      await this.execQuery();
    },
    async filterItems() {
      if (this.filters.employee && this.filters.date) {
        return navigate('/attendance/viewAttendanceRecord', undefined, {
          employeeId: this.filters.employee.id,
          date: this.filters.date,
        });
      }
      await this.execQuery();
    },
    onClickView(item) {
      navigate('/attendance/viewAttendanceRecord', undefined, {
        employeeId: item.id,
        date: this.filters.date,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
  .oxd-table-cell-actions > * {
    margin: 0 !important;
  }
}
</style>
