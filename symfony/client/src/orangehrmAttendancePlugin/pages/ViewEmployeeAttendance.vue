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
  <oxd-table-filter :filter-title="$t('time.employee_attendance_records')">
    <oxd-form @submitValid="filterItems">
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
    <div v-if="showAddButton" class="orangehrm-header-container">
      <oxd-button
        icon-name="plus"
        display-type="secondary"
        :label="$t('time.add_attendance_records')"
        @click="onClickAdd"
      />
    </div>
    <table-header
      :total="total"
      :loading="isLoading"
      :show-divider="showAddButton"
      :selected="checkedItems.length"
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
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import {required} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

const defaultFilters = {
  date: null,
  employee: null,
};

const attendanceRecordNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      empName: `${item.employee?.firstName} ${item.employee?.lastName}
          ${
            item.employee?.terminationId ? this.$t('general.past_employee') : ''
          }`,
      punchIn: `${item.records.in.date} ${item.records.in.time} ${item.records.in.timezone}`,
      punchOut: `${item.records.out.date} ${item.records.out.time} ${item.records.out.timezone}`,
      punchInNote: item.records.in.note,
      punchOutNote: item.records.out.note,
      duration: item.duration,
      total: item.total,
    };
  });
};

export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  setup() {
    const rules = {
      employee: [],
      date: [required],
    };
    const filters = ref({...defaultFilters});

    const serializedFilters = computed(() => {
      return {
        date: filters.value.date,
        empNumber: filters.value.employee?.id,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/attendance/employees/records',
    );
    const {
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
      currentPage,
      showPaginator,
    } = usePaginate(http, {
      query: serializedFilters,
      normalizer: attendanceRecordNormalizer,
      prefetch: false,
    });

    return {
      http,
      rules,
      total,
      pages,
      filters,
      pageSize,
      isLoading,
      execQuery,
      currentPage,
      showPaginator,
      items: response,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'empName',
          slot: 'title',
          title: this.$t('general.employee_name'),
          style: {flex: 1},
        },
        {
          name: 'punchIn',
          title: this.$t('time.punch_in'),
          style: {flex: 1},
        },
        {
          name: 'punchInNote',
          title: this.$t('time.punch_in_note'),
          style: {flex: 1},
        },
        {
          name: 'punchOut',
          title: this.$t('time.punch_out'),
          style: {flex: 1},
        },
        {
          name: 'punchOutNote',
          title: this.$t('time.punch_out_note'),
          style: {flex: 1},
        },
        {
          name: 'duration',
          title: this.$t('time.duration_hours'),
          style: {flex: 1},
        },
        {
          name: 'total',
          title: this.$t('time.total_hours'),
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
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

  computed: {
    showAddButton() {
      // TODO: Check BE config/permission
      return this.filters.employee ? true : false;
    },
  },

  methods: {
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
    onClickAdd() {
      if (!this.filters.employee) return;
      // do something
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
