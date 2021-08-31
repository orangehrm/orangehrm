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
    <slot :filters="filters" :filterItems="filterItems"></slot>
    <br />
    <div class="orangehrm-paper-container">
      <leave-list-table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
      ></leave-list-table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          :clickable="false"
          :loading="isLoading"
          v-model:selected="checkedItems"
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
  </div>
  <leave-comment-modal
    v-if="showCommentModal"
    :leave-id="commentModalState"
    @close="onCommentModalClose"
  >
  </leave-comment-modal>
</template>

<script>
import {computed, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import LeaveListTableHeader from '@/orangehrmLeavePlugin/components/LeaveListTableHeader';
import LeaveCommentsModal from '@/orangehrmLeavePlugin/components/LeaveCommentsModal';

const leavelistNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      empNumber: item.empNumber,
      date: `${item.fromDate} to ${item.toDate}`,
      employeeName: `${item.employee?.firstName} ${item.employee?.lastName}`,
      leaveType: item.leaveType.name,
      leaveBalance: item.leaveBalance,
      days: item.leaveDays,
      status: item.leaveStatus?.name,
      comment: item.comment,
    };
  });
};

const defaultFilters = {
  employee: null,
  fromDate: null,
  toDate: null,
  statuses: [{id: 3, label: 'Pending Approval'}],
  subunit: null,
  includePastEmps: false,
};

export default {
  name: 'leave-list-table',

  components: {
    'leave-list-table-header': LeaveListTableHeader,
    'leave-comment-modal': LeaveCommentsModal,
  },

  data() {
    return {
      headers: [
        {name: 'date', title: 'Date', style: {flex: 1}},
        {name: 'employeeName', title: 'Employee Name', style: {flex: 1}},
        {name: 'leaveType', title: 'Leave Type', style: {flex: 1}},
        {name: 'leaveBalance', title: 'Leave Balance (Days)', style: {flex: 1}},
        {name: 'days', title: 'Number of Days', style: {flex: 1}},
        {name: 'status', title: 'Status', style: {flex: 1}},
        {name: 'comment', title: 'Comments', style: {flex: 1}},
        {
          name: 'action',
          slot: 'footer',
          title: 'Actions',
          style: {flex: '20%'},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            accept: {
              component: 'oxd-button',
              props: {
                label: 'Approve',
                displayType: 'label',
                size: 'medium',
              },
            },
            reject: {
              component: 'oxd-button',
              props: {
                label: 'Reject',
                displayType: 'label-danger',
                size: 'medium',
              },
            },
            more: {
              component: 'oxd-table-dropdown',
              onClick: this.onLeaveRequestAction,
              props: {
                options: [
                  {label: 'Add Comment', context: 'add_comment'},
                  {label: 'View Leave Details', context: 'leave_details'},
                  {label: 'View PIM Info', context: 'pim_details'},
                  {label: 'Cancel Leave', context: 'cancel_leave'},
                ],
              },
            },
          },
        },
      ],
      checkedItems: [],
      showCommentModal: false,
      commentModalState: null,
    };
  },

  setup() {
    const filters = ref({...defaultFilters});

    const serializedFilters = computed(() => {
      const statuses = Array.isArray(filters.value.statuses)
        ? filters.value.statuses
        : [];

      return {
        employeeId: filters.value.employee?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        subunitId: filters.value.subunit?.id,
        includePastEmps: filters.value.includePastEmps,
        leaveStatuses: statuses.map(item => item.id),
      };
    });

    const http = new APIService(
      // window.appGlobal.baseUrl,
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      'api/v2/leave/leave-list',
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
      normalizer: leavelistNormalizer,
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
    onLeaveRequestAction(item, event) {
      switch (event.context) {
        case 'add_comment':
          this.commentModalState = item.id;
          this.showCommentModal = true;
          break;
        case 'cancel_leave':
          this.onLeaveCancel();
          break;
        case 'pim_details':
          navigate('/pim/viewPersonalDetails/empNumber/{id}', {
            id: item.empNumber,
          });
          break;
        default:
          navigate('/leave/viewLeaveRequest/{id}', {id: item.id});
      }
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
    onCommentModalClose() {
      this.commentModalState = null;
      this.showCommentModal = false;
      this.resetDataTable();
    },
    onLeaveCancel() {
      // do nothing.
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
}
::v-deep(.oxd-table-cell-actions) {
  align-items: center;
}
</style>
