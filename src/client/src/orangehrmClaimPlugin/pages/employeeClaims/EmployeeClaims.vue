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
  <oxd-table-filter :filter-title="$t('claim.employee_claims')">
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
            <reference-id-autocomplete
              v-model="filters.referenceId"
              :is-assigned="true"
              :label="$t('claim.reference_id')"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <claim-event-dropdown
              v-model="filters.claimEvent"
              :label="$t('claim.event_name')"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <status-dropdown
              v-model="filters.status"
              :label="$t('general.status')"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              v-model="filters.fromDate"
              :label="$t('general.from_date')"
              :rules="rules.date"
              :years="yearsArray"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="filters.toDate"
              :label="$t('general.to_date')"
              :rules="rules.toDate"
              :years="yearsArray"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <include-employee-dropdown v-model="filters.includeEmployees">
            </include-employee-dropdown>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions>
        <oxd-button
          display-type="ghost"
          :label="$t('general.reset')"
          @click="onClickReset"
        />
        <oxd-button
          class="orangehrm-left-space"
          display-type="secondary"
          :label="$t('general.search')"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-table-filter>
  <br />
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-button
        :label="$t('claim.assign_claim')"
        icon-name="plus"
        display-type="secondary"
        @click="onClickAdd"
      />
    </div>
    <table-header :total="total" :loading="isLoading" />
    <div class="orangehrm-container">
      <oxd-card-table
        v-model:order="sortDefinition"
        :items="items.data"
        :headers="headers"
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
import {ref, computed} from 'vue';
import {navigate} from '@/core/util/helper/navigation';
import useSort from '@ohrm/core/util/composable/useSort';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import ReferenceIdAutocomplete from '@/orangehrmClaimPlugin/components/ReferenceIdAutocomplete.vue';
import ClaimEventDropdown from '@/orangehrmClaimPlugin/components/ClaimEventDropdown.vue';
import StatusDropdown from '@/orangehrmClaimPlugin/components/StatusDropdown.vue';
import {
  shouldNotExceedCharLength,
  validDateFormat,
} from '@/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import useLocale from '@/core/util/composable/useLocale';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import {
  validSelection,
  endDateShouldBeAfterStartDate,
} from '@/core/util/validation/rules';
import IncludeEmployeeDropdownVue from '@ohrm/core/components/dropdown/IncludeEmployeeDropdown.vue';
import usei18n from '@/core/util/composable/usei18n';

const defaultFilters = {
  referenceId: '',
  employee: null,
  claimEvent: null,
  status: null,
  fromDate: null,
  toDate: null,
};

const defaultSortOrder = {
  'claimRequest.referenceId': 'DESC',
  'employee.firstName': 'ASC',
  'claimEvent.name': 'ASC',
  'claimRequest.status': 'ASC',
  'claimRequest.submittedDate': 'ASC',
};

export default {
  components: {
    'reference-id-autocomplete': ReferenceIdAutocomplete,
    'claim-event-dropdown': ClaimEventDropdown,
    'status-dropdown': StatusDropdown,
    'employee-autocomplete': EmployeeAutocomplete,
    'include-employee-dropdown': IncludeEmployeeDropdownVue,
  },
  props: {
    empNumber: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const {$t} = usei18n();
    const filters = ref({
      includeEmployees: {
        id: 1,
        param: 'onlyCurrent',
        label: $t('general.current_employees_only'),
      },
      ...defaultFilters,
    });
    const {$tEmpName} = useEmployeeNameTranslate();
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();

    const serializedFilters = computed(() => {
      return {
        referenceId:
          typeof filters.value.referenceId === 'object' &&
          filters.value.referenceId
            ? filters.value.referenceId.id
            : typeof filters.value.referenceId === 'string'
            ? filters.value.referenceId
            : null,
        empNumber: filters.value.employee?.id,
        eventId: filters.value.claimEvent ? filters.value.claimEvent?.id : null,
        status: filters.value.status ? filters.value.status?.id : null,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        includeEmployees: filters.value.includeEmployees?.param,
        sortField:
          sortField.value === 'claimRequest.claimEvent.name'
            ? 'claimEvent.name'
            : sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const claimRequestDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          referenceId: item.referenceId,
          employee: $tEmpName(item.employee),
          eventName: item.claimEvent.name,
          description: item.description,
          currency: item.currencyType.name,
          status:
            item.status.charAt(0).toUpperCase() +
            item.status.slice(1).toLowerCase(),
          submittedDate: formatDate(
            parseDate(item.submittedDate),
            jsDateFormat,
            {locale},
          ),
          amount: Number(item.amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          }),
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/claim/employees/requests',
    );
    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {
      normalizer: claimRequestDataNormalizer,
      query: serializedFilters,
    });
    onSort(execQuery);
    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
      response,
      filters,
      sortDefinition,
      useDateFormat,
    };
  },
  data() {
    return {
      headers: [
        {
          name: 'referenceId',
          title: this.$t('claim.reference_id'),
          slot: 'title',
          sortField: 'claimRequest.referenceId',
          style: {flex: 3},
        },
        {
          name: 'employee',
          title: this.$t('general.employee_name'),
          slot: 'title',
          sortField: 'employee.firstName',
          style: {flex: 4},
        },
        {
          name: 'eventName',
          title: this.$t('claim.event_name'),
          slot: 'title',
          cellType: 'oxd-table-cell-truncate',
          sortField: 'claimEvent.name',
          style: {flex: 3},
        },
        {
          name: 'description',
          title: this.$t('general.description'),
          slot: 'title',
          cellType: 'oxd-table-cell-truncate',
          sortField: 'claimRequest.description',
          style: {flex: 4},
        },
        {
          name: 'currency',
          title: this.$t('general.currency'),
          slot: 'title',
          style: {flex: 3},
        },
        {
          name: 'submittedDate',
          title: this.$t('claim.submitted_date'),
          slot: 'title',
          sortField: 'claimRequest.submittedDate',
          style: {flex: 3},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          sortField: 'claimRequest.status',
          style: {flex: 2},
        },
        {
          name: 'amount',
          title: this.$t('general.amount'),
          slot: 'title',
          sortField: 'claimRequest.amount',
          style: {flex: 3},
        },
        {
          name: 'actions',
          slot: 'right',
          title: this.$t('general.actions'),
          style: {flex: 4},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            view: {
              onClick: this.onClickView,
              component: 'oxd-button',
              props: {
                label: this.$t('claim.view_details'),
                displayType: 'text',
                size: 'medium',
              },
            },
          },
        },
      ],
      rules: {
        date: [validDateFormat(this.userDateFormat)],
        toDate: [
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(
            () => this.filters.fromDate,
            this.$t('general.to_date_should_be_after_from_date'),
            {allowSameDate: true},
          ),
        ],
        employee: [shouldNotExceedCharLength(100), validSelection],
      },
    };
  },

  methods: {
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
    onClickReset() {
      this.filters = {...defaultFilters};
      this.filterItems();
    },
    onClickAdd() {
      navigate('/claim/assignClaim');
    },
    onClickView(item) {
      navigate('/claim/assignClaim/id/{id}', {id: item.id});
    },
  },
};
</script>
