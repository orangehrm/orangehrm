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
              class="orangehrm-timesheet-table-header-cell"
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
              v-if="totals"
              class="orangehrm-timesheet-table-header-cell --freeze-right"
            >
              {{ $t('general.total') }}
            </th>
          </tr>
        </thead>

        <tbody class="orangehrm-timesheet-table-body">
          <!-- timesheet activities -->
          <tr
            v-for="(record, i) in records"
            :key="record.id"
            class="orangehrm-timesheet-table-body-row"
          >
            <td :class="fixedCellClasses">
              <project-autocomplete
                v-if="editable"
                :model-value="record.project"
                @update:modelValue="updateProject($event, i)"
              />
              <span v-else>{{ record.project.label }}</span>
            </td>
            <td class="orangehrm-timesheet-table-body-cell">
              <activity-dropdown
                v-if="editable"
                :project-id="record.project && record.project.id"
                :model-value="record.activity"
                @update:modelValue="updateActivity($event, i)"
              />
              <span v-else>{{ record.activity.label }}</span>
            </td>
            <td
              v-for="day in record.days"
              :key="day.id"
              :class="{
                'orangehrm-timesheet-table-body-cell': true,
                '--highlight-3': !editable && !day.workday,
              }"
            >
              <oxd-icon-button
                name="chat-dots"
                class="orangehrm-timesheet-icon-comment"
                @click="viewComment(day)"
              />
              <oxd-input-field
                v-if="editable"
                :model-value="day.trackedTime"
                @update:modelValue="updateTime($event, i, day)"
              />
              <span v-else>
                {{ day.trackedTime ?? '00:00' }}
              </span>
            </td>
            <td
              v-if="totals"
              class="orangehrm-timesheet-table-body-cell --freeze-right --highlight"
            >
              {{ record.totalTrackedTime }}
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
          <tr v-if="totals" class="orangehrm-timesheet-table-body-row --total">
            <td
              class="orangehrm-timesheet-table-body-cell --freeze-left --highlight"
            >
              {{ $t('general.total') }}
            </td>
            <td></td>
            <!-- total per day -->
            <td
              v-for="(total, key) in totals"
              :key="key"
              class="orangehrm-timesheet-table-body-cell"
            >
              {{ total }}
            </td>
            <!-- total per day -->
            <td
              class="orangehrm-timesheet-table-body-cell --freeze-right --highlight-2"
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
import {nanoid} from 'nanoid';
import {parseDate} from '@ohrm/core/util/helper/datefns';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner.vue';
import ActivityDropdown from '@/orangehrmTimePlugin/components/ActivityDropdown.vue';
import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';
import TimesheetCommentModal from '@/orangehrmTimePlugin/components/TimesheetCommentModal.vue';

export default {
  name: 'Timesheet',

  components: {
    'oxd-loading-spinner': Spinner,
    'activity-dropdown': ActivityDropdown,
    'project-autocomplete': ProjectAutocomplete,
    'timesheet-comment-modal': TimesheetCommentModal,
  },

  props: {
    days: {
      type: Array,
      default: () => [],
    },
    records: {
      type: Array,
      default: () => [],
    },
    totals: {
      type: Object,
      required: false,
      default: () => ({}),
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
      return Array.isArray(this.days)
        ? this.days.map(day => {
            const date = parseDate(day, 'yyyy-MM-dd');
            return {
              id: date.valueOf(),
              day: date.getDate(),
              title: days[date.getDay()],
            };
          })
        : [];
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
      const days = this.days.map(date => {
        return {
          id: nanoid(8),
          date: date,
          comment: null,
          trackedTime: null,
        };
      });
      const updated = [
        ...this.records,
        {
          id: nanoid(8),
          project: null,
          activity: null,
          days,
        },
      ];
      this.syncRecords(updated);
    },
    updateTime($value, index, date) {
      const updated = this.records.map((record, i) => {
        if (i === index) {
          for (let x = 0; x < record.days.length; x++) {
            if (record.days[x] === date) {
              record.days[x].trackedTime = $value;
              break;
            }
          }
        }
        return record;
      });
      this.syncRecords(updated);
    },
    updateProject($value, index) {
      const updated = this.records.map((record, i) => {
        if (i === index) {
          record.project = $value;
        }
        return record;
      });
      this.syncRecords(updated);
    },
    updateActivity($value, index) {
      const updated = this.records.map((record, i) => {
        if (i === index) {
          record.activity = $value;
        }
        return record;
      });
      this.syncRecords(updated);
    },
    viewComment(day) {
      this.commentModalState = day.date;
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
  },
};
</script>

<style src="./timesheet.scss" lang="scss" scoped></style>
