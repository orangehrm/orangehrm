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
  <oxd-table-filter :filter-title="$t('time.my_attendance_records')">
    <oxd-form @submitValid="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
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
    <div class="orangehrm-header-container">
      <oxd-text class="orangehrm-header-total" tag="span">
        {{ $t('time.total_duration') }}: {{ totalDuration }}
      </oxd-text>
    </div>
    <table-header
      :total="total"
      :loading="isLoading"
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
        class="orangehrm-my-attendance"
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
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

const defaultFilters = {
  date: null,
};

const attendanceRecordNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      punchIn: `${item.records.in.date} ${item.records.in.time} ${item.records.in.timezone}`,
      punchOut: `${item.records.out.date} ${item.records.out.time} ${item.records.out.timezone}`,
      punchInNote: item.records.in.note,
      punchOutNote: item.records.out.note,
      duration: item.duration,
    };
  });
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },

  setup() {
    const rules = {
      date: [required],
    };
    const filters = ref({...defaultFilters});

    const serializedFilters = computed(() => {
      return {
        date: filters.value.date,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/attendance/records',
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

    const totalDuration = computed(() => {
      const meta = response.value?.meta;
      return meta ? parseFloat(meta.total).toFixed(2) : '0.00';
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
      totalDuration,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'punchIn',
          title: 'Punch In',
          style: {flex: 1},
        },
        {
          name: 'punchInNote',
          title: 'Punch In Note',
          style: {flex: 1},
        },
        {
          name: 'punchOut',
          title: 'Punch Out',
          style: {flex: 1},
        },
        {
          name: 'punchOutNote',
          title: 'Punch Out Note',
          style: {flex: 1},
        },
        {
          name: 'duration',
          title: 'Duration (Hours)',
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'title',
          title: 'Actions',
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

<style src="./view-my-attendance.scss" lang="scss" scoped></style>
