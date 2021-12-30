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
  <oxd-form class="orangehrm-paper-container">
    <div class="orangehrm-timesheet-header">
      <div class="orangehrm-timesheet-header--title">
        <slot name="header-title"></slot>
      </div>
      <div class="orangehrm-timesheet-header--options">
        <slot name="header-options"></slot>
      </div>
    </div>

    <div v-if="loading" class="orangehrm-timesheet-loader">
      <oxd-loading-spinner />
    </div>
    <div
      v-else-if="!loading && !columns"
      class="orangehrm-timesheet-body-message"
    >
      <oxd-alert
        type="warn"
        :show="true"
        :message="$t('time.no_timesheets_found')"
      ></oxd-alert>
    </div>

    <div v-else class="orangehrm-timesheet-body">
      <table :class="tableClasses">
        <thead class="orangehrm-timesheet-table-header">
          <tr class="orangehrm-timesheet-table-header-row">
            <th :class="fixedColumnClasses">
              {{ $t('time.project') }}
            </th>
            <th class="orangehrm-timesheet-table-header-cell">
              {{ $t('time.activity') }}
            </th>

            <!-- timesheet days of week -->
            <th
              v-for="day in daysOfWeek"
              :key="day.id"
              class="orangehrm-timesheet-table-header-cell --center"
            >
              <span class="--day">
                {{ day.day }}
              </span>
              <span>
                {{ day.title }}
              </span>
            </th>
            <!-- timesheet days of week -->

            <th
              v-if="!editable"
              class="orangehrm-timesheet-table-header-cell --center --freeze-right"
            >
              {{ $t('general.total') }}
            </th>
          </tr>
        </thead>

        <tbody class="orangehrm-timesheet-table-body">
          <!-- timesheet activities -->
          <tr
            v-for="(record, i) in records"
            :key="`${record.project}_${record.activity}`"
            class="orangehrm-timesheet-table-body-row"
          >
            <td :class="fixedCellClasses">
              <project-autocomplete
                v-if="editable"
                :model-value="getProject(record.project)"
                @update:modelValue="updateProject($event, i)"
              />
              <span v-else>{{ record.project && record.project.name }}</span>
            </td>
            <td class="orangehrm-timesheet-table-body-cell">
              <activity-dropdown
                v-if="editable"
                :project-id="record.project && record.project.id"
                :model-value="getActivity(record.activity)"
                @update:modelValue="updateActivity($event, i)"
              />
              <span v-else>{{ record.activity && record.activity.name }}</span>
            </td>
            <td
              v-for="(column, date) in columns"
              :key="`${record.project}_${record.activity}_${date}`"
              :class="{
                'orangehrm-timesheet-table-body-cell': true,
                '--center': true,
                '--highlight-3': !editable && column.workday,
              }"
            >
              <oxd-icon-button
                v-if="record.dates[date] && record.dates[date].comment"
                name="chat-dots"
                class="orangehrm-timesheet-icon-comment"
                @click="viewComment(date)"
              />
              <oxd-input-field
                v-if="editable"
                :model-value="
                  record.dates[date] ? record.dates[date].duration : null
                "
                @update:modelValue="updateTime($event, i, date)"
              />
              <span v-else>
                {{ record.dates[date] ? record.dates[date].duration : '00:00' }}
              </span>
            </td>
            <td
              v-if="!editable"
              class="orangehrm-timesheet-table-body-cell --center --freeze-right --highlight"
            >
              {{ record.total.label }}
            </td>
            <td v-if="editable" class="orangehrm-timesheet-table-body-cell">
              <oxd-icon-button
                name="trash"
                class="orangehrm-timesheet-icon"
                @click="deleteRow(i)"
              />
            </td>
          </tr>
          <!-- timesheet activities -->

          <!-- totals -->
          <tr
            v-if="!editable && records.length > 0"
            class="orangehrm-timesheet-table-body-row --total"
          >
            <td
              class="orangehrm-timesheet-table-body-cell --freeze-left --highlight"
            >
              {{ $t('general.total') }}
            </td>
            <td></td>
            <!-- total per day -->
            <td
              v-for="date in columns"
              :key="`total-${date}`"
              class="orangehrm-timesheet-table-body-cell --center"
            >
              {{ date.total.label }}
            </td>
            <!-- total per day -->
            <td
              class="orangehrm-timesheet-table-body-cell --center --freeze-right --highlight-2"
            >
              {{ subtotal }}
            </td>
          </tr>
          <!-- totals -->

          <!-- add row -->
          <tr v-if="editable" class="orangehrm-timesheet-table-body-row">
            <td class="orangehrm-timesheet-table-body-cell --flex">
              <oxd-icon-button
                name="plus"
                class="orangehrm-timesheet-icon"
                @click="addRow"
              />
              <oxd-text type="subtitle-2">
                {{ $t('time.add_row') }}
              </oxd-text>
            </td>
          </tr>
          <!-- add row -->

          <tr
            v-if="records.length === 0"
            class="orangehrm-timesheet-table-body-row"
          >
            <td colspan="9" class="orangehrm-timesheet-table-body-cell">
              {{ $t('general.no_records_found') }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="orangehrm-timesheet-footer">
      <div class="orangehrm-timesheet-footer--title">
        <slot name="footer-title"></slot>
      </div>
      <div class="orangehrm-timesheet-footer--options">
        <slot name="footer-options"></slot>
      </div>
    </div>

    <timesheet-comment-modal
      v-if="showCommentModal"
      :date="commentModalState"
      :editable="editable"
      @close="onCommentModalClose"
    ></timesheet-comment-modal>
  </oxd-form>
</template>

<script>
import {parseDate} from '@ohrm/core/util/helper/datefns';
import Alert from '@ohrm/oxd/core/components/Alert/Alert';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner.vue';
import ActivityDropdown from '@/orangehrmTimePlugin/components/ActivityDropdown.vue';
import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';
import TimesheetCommentModal from '@/orangehrmTimePlugin/components/TimesheetCommentModal.vue';

export default {
  name: 'Timesheet',

  components: {
    'oxd-alert': Alert,
    'oxd-loading-spinner': Spinner,
    'activity-dropdown': ActivityDropdown,
    'project-autocomplete': ProjectAutocomplete,
    'timesheet-comment-modal': TimesheetCommentModal,
  },

  props: {
    records: {
      type: Array,
      default: () => [],
    },
    columns: {
      type: Object,
      required: false,
      default: () => null,
    },
    subtotal: {
      type: String,
      required: false,
      default: null,
    },
    editable: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['update:records'],

  data() {
    return {
      showCommentModal: false,
      commentModalState: null,
    };
  },

  computed: {
    days() {
      return this.columns ? Object.keys(this.columns) : [];
    },
    daysOfWeek() {
      const days = [
        this.$t('general.sun'),
        this.$t('general.mon'),
        this.$t('general.tue'),
        this.$t('general.wed'),
        this.$t('general.thu'),
        this.$t('general.fri'),
        this.$t('general.sat'),
      ];
      return this.days.map(day => {
        const date = parseDate(day, 'yyyy-MM-dd');
        return {
          id: date.valueOf(),
          day: date.getDate(),
          title: days[date.getDay()],
        };
      });
    },
    tableClasses() {
      return {
        'orangehrm-timesheet-table': true,
        '--editable': this.editable,
      };
    },
    fixedColumnClasses() {
      return {
        'orangehrm-timesheet-table-header-cell': true,
        '--freeze-left': !this.editable,
      };
    },
    fixedCellClasses() {
      return {
        'orangehrm-timesheet-table-body-cell': true,
        '--freeze-left': !this.editable,
      };
    },
  },

  methods: {
    deleteRow(index) {
      const updated = this.records.filter((_, i) => i !== index);
      this.syncRecords(updated);
      this.$nextTick().then(() => {
        if (updated.length === 0) this.addRow();
      });
    },
    addRow() {
      const updated = [
        ...this.records,
        {
          project: null,
          activity: null,
          dates: {},
        },
      ];
      this.syncRecords(updated);
    },
    updateTime($value, index, date) {
      const updated = this.records.map((record, i) => {
        if (i === index) {
          const _date = {
            [date]: {
              date: date,
              comment: null,
              duration: $value,
            },
          };
          record.dates = {...record.dates, ..._date};
        }
        return record;
      });
      this.syncRecords(updated);
    },
    updateProject($value, index) {
      const updated = this.records.map((record, i) => {
        if (i === index) {
          record.project = $value ? {id: $value.id, name: $value.label} : null;
        }
        return record;
      });
      if ($value === null) this.updateActivity(null, index);
      this.syncRecords(updated);
    },
    updateActivity($value, index) {
      const updated = this.records.map((record, i) => {
        if (i === index) {
          record.activity = $value ? {id: $value.id, name: $value.label} : null;
        }
        return record;
      });
      this.syncRecords(updated);
    },
    viewComment(date) {
      this.commentModalState = date;
      this.showCommentModal = true;
    },
    syncRecords(updated) {
      if (!this.editable) return;
      this.$emit('update:records', updated);
    },
    onCommentModalClose() {
      this.showCommentModal = false;
      this.commentModalState = null;
    },
    getProject(project) {
      return project ? {id: project.id, label: project.name} : null;
    },
    getActivity(activity) {
      return activity ? {id: activity.id, label: activity.name} : null;
    },
  },
};
</script>

<style src="./timesheet.scss" lang="scss" scoped></style>
