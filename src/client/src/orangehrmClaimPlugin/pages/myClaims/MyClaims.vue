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
  <oxd-table-filter :filter-title="$t('claim.events')">
    <oxd-form @submit-valid="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <reference-id-autocomplete
              v-model="filters.referenceId"
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
              :years="yearsArray"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="filters.toDate"
              :label="$t('general.to_date')"
              :years="yearsArray"
              required
            />
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
        :label="$t('general.add')"
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
import usei18n from '@/core/util/composable/usei18n';
import {navigate} from '@/core/util/helper/navigation';
import useSort from '@ohrm/core/util/composable/useSort';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import ReferenceIdAutocomplete from '@/orangehrmClaimPlugin/components/ReferenceIdAutocomplete.vue';
import ClaimEventDropdown from '@/orangehrmClaimPlugin/components/ClaimEventDropdown.vue';
import StatusDropdown from '@/orangehrmClaimPlugin/components/StatusDropdown.vue';

const defaultFilters = {
  referenceId: null,
  claimEvent: null,
  status: null,
  fromDate: null,
  toDate: null,
};

const defaultSortOrder = {
  'claimEvent.name': 'ASC',
  'claimEvent.status': 'DESC',
};

export default {
  components: {
    'reference-id-autocomplete': ReferenceIdAutocomplete,
    'claim-event-dropdown': ClaimEventDropdown,
    'status-dropdown': StatusDropdown,
  },
  setup() {
    const filters = ref({...defaultFilters});
    const {$t} = usei18n();
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        // referenceId:
        //   typeof filters.value.referenceId === 'object'
        //     ? filters.value.referenceId.label
        //     : typeof filters.value.referenceId === 'string'
        //     ? filters.value.referenceId
        //     : null,
        eventId: filters.value.claimEvent ? filters.value.claimEvent?.id : null,
        status: filters.value.status ? filters.value.status?.id : null,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const claimRequestDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          referenceId: item.referenceId,
          eventName: item.claimEvent.name,
          description: item.description,
          currency: item.currencyType.name,
          status:
            item.status.charAt(0).toUpperCase() +
            item.status.slice(1).toLowerCase(),
          submittedDate: item.submittedDate,
          amount: parseFloat(item.amount).toFixed(2),
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/claim/requests',
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
    console.log('response', response);
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
          name: 'eventName',
          title: this.$t('claim.event_name'),
          slot: 'title',
          sortField: 'claimRequest.claimEvent.name',
          style: {flex: 3},
        },
        {
          name: 'description',
          title: this.$t('general.description'),
          slot: 'title',
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
          title: this.$t('claim.amount'),
          slot: 'title',
          sortField: 'claimRequest.amount',
          style: {flex: 3},
        },
        {
          name: 'actions',
          slot: 'title',
          title: this.$t('general.actions'),
          style: {flex: '2'},
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
      checkedItems: [],
      ClaimEventStatuses: [
        {id: 1, label: this.$t('general.active')},
        {id: 0, label: this.$t('performance.inactive')},
      ],
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
      navigate('/claim/saveEvents');
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach((index) => {
        ids.push(this.items?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    deleteItems(items) {
      if (items instanceof Array) {
        this.isLoading = true;
        this.http
          .deleteAll({
            ids: items,
          })
          .then(() => {
            return this.$toast.deleteSuccess();
          })
          .then(() => {
            this.isLoading = false;
            this.resetDataTable();
          });
      }
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    onClickEdit(item) {
      navigate('/claim/saveEvents/{id}', {id: item.id});
    },
    onClickView(item) {
      navigate('/claim/submitClaim/id/{id}', {id: item.id});
    },
  },
};
</script>
