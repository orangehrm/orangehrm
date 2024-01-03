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
  <oxd-table-filter :filter-title="$t('claim.events')">
    <oxd-form @submit-valid="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <event-autocomplete
              v-model="filters.name"
              :label="$t('claim.event_name')"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="filters.status"
              type="select"
              :label="$t('general.status')"
              :options="ClaimEventStatuses"
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
    <table-header
      :total="total"
      :loading="isLoading"
      :selected="checkedItems.length"
      @delete="onClickDeleteSelected"
    />
    <div class="orangehrm-container">
      <oxd-card-table
        v-model:selected="checkedItems"
        v-model:order="sortDefinition"
        :items="items.data"
        :headers="headers"
        :selectable="true"
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
  <delete-confirmation ref="deleteDialog"></delete-confirmation>
</template>

<script>
import {ref, computed} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import {navigate} from '@/core/util/helper/navigation';
import useSort from '@ohrm/core/util/composable/useSort';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import EventAutocomplete from '@/orangehrmClaimPlugin/components/EventAutocomplete.vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';

const defaultFilters = {
  name: '',
  status: null,
};

const defaultSortOrder = {
  'claimEvent.name': 'ASC',
  'claimEvent.status': 'DESC',
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'event-autocomplete': EventAutocomplete,
  },
  setup() {
    const filters = ref({...defaultFilters});
    const {$t} = usei18n();
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        eventId:
          typeof filters.value.name === 'object' ? filters.value.name.id : null,
        name:
          typeof filters.value.name === 'string' ? filters.value.name : null,
        status: filters.value.status ? filters.value.status?.id === 1 : null,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const claimEventDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          name: item.name,
          description: item.description,
          status: item.status
            ? $t('general.active')
            : $t('performance.inactive'),
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/claim/events',
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
      normalizer: claimEventDataNormalizer,
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
    };
  },
  data() {
    return {
      headers: [
        {
          name: 'name',
          title: this.$t('claim.event_name'),
          slot: 'title',
          sortField: 'claimEvent.name',
          style: {flex: 3},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          sortField: 'claimEvent.status',
          style: {flex: 2},
        },
        {
          name: 'actions',
          title: this.$t('general.actions'),
          slot: 'action',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
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
  },
};
</script>
