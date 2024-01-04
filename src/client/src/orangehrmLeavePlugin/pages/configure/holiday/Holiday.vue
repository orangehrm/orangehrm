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
  <div class="orangehrm-background-container">
    <oxd-table-filter :filter-title="$t('leave.holidays')">
      <oxd-form @submit-valid="filterItems" @reset="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                v-model="filters.fromDate"
                :label="$t('general.from')"
                :rules="rules.fromDate"
                :years="yearArray"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="filters.toDate"
                :label="$t('general.to')"
                :rules="rules.toDate"
                :years="yearArray"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            :label="$t('general.reset')"
            type="reset"
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
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          :headers="headers"
          :items="items?.data"
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
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {
  endDateShouldBeAfterStartDate,
  required,
  validDateFormat,
} from '@/core/util/validation/rules';
import {yearRange} from '@ohrm/core/util/helper/year-range';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

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

    const {jsDateFormat, userDateFormat} = useDateFormat();
    const {locale} = useLocale();
    const dataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          name: item.name,
          date: formatDate(parseDate(item.date), jsDateFormat, {locale}),
          recurring: item.recurring ? 'Yes' : 'No',
          length: item.lengthName,
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/holidays',
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
      userDateFormat,
    };
  },

  data() {
    return {
      yearArray: [...yearRange(201)],
      rules: {
        fromDate: [required, validDateFormat(this.userDateFormat)],
        toDate: [
          required,
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(
            () => this.filters.fromDate,
            this.$t('general.to_date_should_be_after_from_date'),
            {allowSameDate: true},
          ),
        ],
      },
      headers: [
        {
          name: 'name',
          slot: 'title',
          title: this.$t('general.name'),
          style: {flex: 2},
        },
        {name: 'date', title: this.$t('general.date'), style: {flex: 2}},
        {
          name: 'length',
          title: this.$t('leave.full_day_half_day'),
          style: {flex: 2},
        },
        {
          name: 'recurring',
          title: this.$t('leave.repeats_annually'),
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
      const ids = this.checkedItems.map((index) => {
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
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
  },
};
</script>
