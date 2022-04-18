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
  <oxd-table-filter :filter-title="$t('attendance.my_attendance_records')">
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
        :selectable="isEditable"
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
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {freshDate, formatDate} from '@ohrm/core/util/helper/datefns';
import RecordCell from '@/orangehrmAttendancePlugin/components/RecordCell.vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import {getStandardTimezone} from '@/core/util/helper/datefns';

const attendanceRecordNormalizer = data => {
  return data.map(item => {
    const {punchIn, punchOut} = item;
    return {
      id: item.id,
      punchIn: punchIn,
      punchOut: punchOut,
      punchInNote: punchIn.note,
      punchOutNote: punchOut.note,
      duration: item.duration,
    };
  });
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    date: {
      type: String,
      default: null,
    },
    isEditable: {
      type: Boolean,
      default: false,
    },
  },

  setup(props) {
    const rules = {
      date: [required],
    };
    const filters = ref({
      date: props.date ? props.date : formatDate(freshDate(), 'yyyy-MM-dd'),
    });

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
    });

    const totalDuration = computed(() => {
      const meta = response.value?.meta;
      return meta ? meta.sum.label : '0.00';
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
          slot: 'title',
          title: this.$t('attendance.punch_in'),
          style: {flex: 1},
          cellRenderer: this.cellRenderer,
        },
        {
          name: 'punchInNote',
          slot: 'title',
          title: this.$t('attendance.punch_in_note'),
          style: {flex: 1},
        },
        {
          name: 'punchOut',
          slot: 'title',
          title: this.$t('attendance.punch_out'),
          style: {flex: 1},
          cellRenderer: this.cellRenderer,
        },
        {
          name: 'punchOutNote',
          slot: 'title',
          title: this.$t('attendance.punch_out_note'),
          style: {flex: 1},
        },
        {
          name: 'duration',
          slot: 'title',
          title: this.$t('attendance.duration_hours'),
          style: {flex: 1},
        },
        {
          ...(this.isEditable && {
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
                onClick: this.onClickEdit,
                props: {
                  name: 'pencil-fill',
                },
              },
            },
          }),
        },
      ],
      checkedItems: [],
    };
  },

  methods: {
    cellRenderer(...args) {
      const cellData = args[1];
      return {
        component: RecordCell,
        props: {
          date: cellData.userDate,
          time: cellData.userTime,
          offset: getStandardTimezone(cellData.offset),
        },
      };
    },
    onClickEdit(item) {
      navigate('/attendance/editAttendanceRecord/{id}', {id: item.id});
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
  },
};
</script>

<style src="./view-my-attendance.scss" lang="scss" scoped></style>
