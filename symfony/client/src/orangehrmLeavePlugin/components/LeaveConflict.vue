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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ header }}
      </oxd-text>
    </div>
    <table-header
      :loading="false"
      :selected="0"
      :total="data.length"
    ></table-header>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items"
        :clickable="false"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div class="orangehrm-bottom-container"></div>
  </div>
  <br />
</template>

<script>
export default {
  name: 'LeaveConflict',
  props: {
    workshiftExceeded: {
      type: Boolean,
      default: false,
    },
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      headers: [
        {
          name: 'date',
          title: this.$t('general.date'),
          style: {flex: 1},
        },
        {
          name: 'hours',
          title: this.$t('leave.no_of_hours'),
          style: {flex: 1},
        },
        {
          name: 'type',
          title: this.$t('leave.leave_type'),
          style: {flex: 1},
        },
        {
          name: 'status',
          title: this.$t('general.status'),
          style: {flex: 1},
        },
        {
          name: 'comments',
          title: this.$t('general.comments'),
          style: {flex: 1},
        },
      ],
    };
  },

  computed: {
    header() {
      return this.workshiftExceeded
        ? this.$t(
            'leave.workshift_length_exceeded_due_to_the_following_leave_request',
          )
        : this.$t('leave.overlapping_leave_request_found');
    },
    items() {
      return this.data.map(item => {
        return {
          date: item.date,
          hours: parseFloat(item.lengthHours).toFixed(2),
          type: item.leaveType?.name,
          status: item.status?.name,
          comments: item.lastComment?.comment,
        };
      });
    },
  },
};
</script>
