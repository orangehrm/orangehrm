<template>
  <div>
    <oxd-button
      :label="$t('general.add')"
      icon-name="plus"
      display-type="secondary"
      @click="() => console.log('Hello')"
    />
  </div>
  <table-header
    :total="total"
    :loading="isLoading"
    :selected="checkedItems.length"
  />
  <div class="orangehrm-container">
    <oxd-card-table
      v-model:selected="checkedItems"
      v-model:order="sortDefinition"
      :items="items?.data"
      :headers="headers"
      :selectable="true"
      :clickable="false"
      :loading="isLoading"
      row-decorator="oxd-table-decorator-card"
    />
  </div>
</template>

<script>
import {ref, computed} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';

const defaultFilters = {
  name: '',
  status: null,
};

const defaultSortOrder = {
  'claimEvent.name': 'ASC',
  'claimEvent.status': 'DESC',
};

export default {
  setup() {
    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        name: filters.value.name,
        status: filters.value.status,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/claim/events',
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
    } = usePaginate(http, {query: serializedFilters});
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

  methods: {
    hi() {
      console.log(this.items);
      //console.log(this.response.data[0]);
    },
  },

  data: () => ({
    headers: [
      {
        name: 'name',
        title: 'Name',
        sortField: 'claimEvent.name',
        style: {'flex-basis': '20%'},
      },
      {
        name: 'status',
        title: 'Status',
        sortField: 'claimEvent.status',
        style: {'flex-basis': '20%'},
      },
      {
        name: 'description',
        title: 'Description',
        style: {'flex-basis': '20%'},
      },
      {
        name: 'actions',
        title: 'Actions',
        slot: 'action',
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {
          delete: {
            onClick: () => {
              console.log('delete');
            },
            component: 'oxd-icon-button',
            props: {
              name: 'trash',
            },
          },
          edit: {
            onClick: () => {
              console.log('edit');
            },
            props: {
              name: 'pencil-fill',
            },
          },
        },
      },
    ],
    checkedItems: [],
  }),
};
</script>

<style></style>
