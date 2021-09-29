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
    <oxd-table-filter :filter-title="$t('leave.holidays')">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                :label="$t('general.from')"
                v-model="filters.fromDate"
                :rules="rules.fromDate"
                :years="yearArray"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                :label="$t('general.to')"
                v-model="filters.toDate"
                :rules="rules.toDate"
                :years="yearArray"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            displayType="ghost"
            :label="$t('general.reset')"
            @click="onClickReset"
          />
          <oxd-button
            class="orangehrm-left-space"
            displayType="secondary"
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
          iconName="plus"
          displayType="secondary"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          :clickable="false"
          v-model:selected="checkedItems"
          :loading="isLoading"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          :length="pages"
          v-model:current="currentPage"
        />
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  endDateShouldBeAfterStartDate,
  required,
  validDateFormat,
} from '@/core/util/validation/rules';
import {yearRange} from '@orangehrm/core/util/helper/year-range';

const dataNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      name: item.name,
      date: item.date,
      recurring: item.recurring ? 'Yes' : 'No',
      length: item.lengthName,
    };
  });
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    leavePeriod: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      yearArray: [...yearRange(201)],
      rules: {
        fromDate: [required, validDateFormat()],
        toDate: [
          required,
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.filters.fromDate,
            'To date should be after from date',
            {allowSameDate: true},
          ),
        ],
      },
      headers: [
        {name: 'name', slot: 'title', title: 'Name', style: {flex: 2}},
        {name: 'date', title: 'Date', style: {flex: 2}},
        {name: 'length', title: 'Full Day/ Half Day', style: {flex: 2}},
        {name: 'recurring', title: 'Repeats Annually', style: {flex: 2}},
        {
          name: 'actions',
          title: 'Actions',
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
    };
  },

  setup(props) {
    const filters = ref({
      fromDate: props.leavePeriod.startDate,
      toDate: props.leavePeriod.endDate,
    });

    const serializedFilters = computed(() => {
      return {
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/holidays',
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
      normalizer: dataNormalizer,
    });

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
    };
  },

  methods: {
    onClickAdd() {
      navigate('/leave/saveHolidays');
    },
    onClickEdit(item) {
      navigate('/leave/saveHolidays/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.items?.data[index].id;
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
      this.filters = {
        fromDate: this.leavePeriod.startDate,
        toDate: this.leavePeriod.endDate,
      };
      this.filterItems();
    },
  },
};
</script>
