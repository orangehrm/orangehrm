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
    <oxd-table-filter :filter-title="$t('admin.locations')">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.name"
                :label="$t('general.name')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.city"
                :label="$t('general.city')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.countryCode"
                type="select"
                :label="$t('general.country')"
                :clear="false"
                :options="countries"
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
        <div>
          <oxd-button
            v-if="$can.create(`locations`)"
            :label="$t('general.add')"
            icon-name="plus"
            display-type="secondary"
            @click="onClickAdd"
          />
        </div>
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
          :selectable="$can.delete(`locations`)"
          :disabled="!($can.delete(`locations`) && $can.update('locations'))"
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
  </div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import {computed, ref} from 'vue';
import useSort from '@ohrm/core/util/composable/useSort';

const defaultFilters = {
  name: '',
  city: '',
  countryCode: {},
};

const defaultSortOrder = {
  'location.name': 'ASC',
  'location.city': 'DEFAULT',
  'country.countryName': 'DEFAULT',
  'location.phone': 'DEFAULT',
  noOfEmployees: 'DEFAULT',
};

const locationDataNormalizer = data => {
  return data.map(location => {
    return {
      id: location.id,
      name: location.name,
      city: location.city,
      country: location.country.countryName,
      phone: location.phone,
      noOfEmployees: location.noOfEmployees ? location.noOfEmployees : 0,
    };
  });
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    countries: {
      type: Array,
      default: () => [],
    },
  },

  setup() {
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const filters = ref({...defaultFilters});
    const serializedFilters = computed(() => {
      return {
        name: filters.value.name,
        city: filters.value.city,
        countryCode: filters.value.countryCode?.id,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/locations',
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
      query: serializedFilters,
      normalizer: locationDataNormalizer,
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
      filters,
      sortDefinition,
    };
  },
  data() {
    const cellConfig = {
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
    };

    return {
      headers: [
        {
          name: 'name',
          slot: 'title',
          title: this.$t('general.name'),
          style: {flex: 1},
          sortField: 'location.name',
        },
        {
          name: 'city',
          title: this.$t('general.city'),
          style: {flex: 1},
          sortField: 'location.city',
        },
        {
          name: 'country',
          title: this.$t('general.country'),
          style: {flex: 1},
          sortField: 'country.countryName',
        },
        {
          name: 'phone',
          title: this.$t('general.phone'),
          style: {flex: 1},
          sortField: 'location.phone',
        },
        {
          name: 'noOfEmployees',
          title: this.$t('admin.number_of_employees'),
          style: {flex: 1},
          sortField: 'noOfEmployees',
        },
        {
          name: 'actions',
          title: this.$t('general.actions'),
          slot: 'action',
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: cellConfig,
        },
      ],
      checkedItems: [],
    };
  },

  methods: {
    onClickAdd() {
      navigate('/admin/saveLocation');
    },
    onClickEdit(item) {
      navigate('/admin/saveLocation/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach(index => {
        ids.push(this.items?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
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
  },
};
</script>
