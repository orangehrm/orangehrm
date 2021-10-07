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
    <slot :filters="filters" :rules="rules" :filterItems="filterItems"></slot>
    <br />
    <div class="orangehrm-paper-container">
      <leave-list-table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        :bulkActions="leaveBulkActions"
        @onActionClick="onLeaveActionBulk"
      >
      </leave-list-table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items?.data"
          :selectable="leaveBulkActions !== null"
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
    :id="commentModalState"
    @close="onCommentModalClose"
  >
  </leave-comment-modal>
  <leave-bulk-action-modal ref="bulkActionModal" :data="bulkActionModalState">
  </leave-bulk-action-modal>
</template>

<script>
import {
  required,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@/core/util/validation/rules';
import {computed, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import useLeaveActions from '@/orangehrmLeavePlugin/util/composable/useLeaveActions';
import LeaveCommentsModal from '@/orangehrmLeavePlugin/components/LeaveCommentsModal';
import LeaveBulkActionModal from '@/orangehrmLeavePlugin/components/LeaveBulkActionModal';
import LeaveListTableHeader from '@/orangehrmLeavePlugin/components/LeaveListTableHeader';

const leavelistNormalizer = data => {
  return data.map(item => {
    let leaveDatePeriod,
      leaveStatuses,
      leaveBalances = '';
    const duration = item.dates.durationType?.type;

    if (item.dates.fromDate) {
      leaveDatePeriod = item.dates.fromDate;
    }
    if (item.dates.toDate) {
      leaveDatePeriod += ` to ${item.dates.toDate}`;
    }
    if (item.dates.startTime && item.dates.endTime) {
      leaveDatePeriod += ` (${item.dates.startTime} - ${item.dates.endTime})`;
    }
    if (duration === 'half_day_morning' || duration === 'half_day_afternoon') {
      leaveDatePeriod += ' Half Day';
    }
    if (Array.isArray(item.leaveBreakdown)) {
      leaveStatuses = item.leaveBreakdown
        .map(
          status =>
            `${status.name} (${parseFloat(status.lengthDays).toFixed(2)})`,
        )
        .join(', ');
    }
    if (Array.isArray(item.leaveBalances)) {
      if (item.leaveBalances.length > 1) {
        leaveBalances = item.leaveBalances
          .map(
            ({period, balance}) => `${parseFloat(balance.balance).toFixed(2)}
         (${period.startDate} - ${period.endDate})`,
          )
          .join(', ');
      } else {
        const balance = item.leaveBalances[0]?.balance.balance;
        leaveBalances = balance ? parseFloat(balance).toFixed(2) : '0.00';
      }
    }

    return {
      id: item.id,
      empNumber: item.employee?.empNumber,
      date: leaveDatePeriod,
      employeeName: `${item.employee?.firstName} ${item.employee?.lastName}
          ${item.employee?.terminationId ? ' (Past Employee)' : ''}`,
      leaveType:
        item.leaveType?.name + `${item.leaveType?.deleted ? ' (Deleted)' : ''}`,
      leaveBalance: leaveBalances,
      days: parseFloat(item.noOfDays).toFixed(2),
      status: leaveStatuses,
      comment: item.lastComment?.comment,
      actions: item.allowedActions,
    };
  });
};

const defaultFilters = {
  employee: null,
  fromDate: null,
  toDate: null,
  statuses: [{id: 3, label: 'Pending Approval', key: 'pendingApproval'}],
  subunit: null,
  includePastEmps: false,
};

export default {
  name: 'leave-list-table',

  components: {
    'leave-list-table-header': LeaveListTableHeader,
    'leave-comment-modal': LeaveCommentsModal,
    'leave-bulk-action-modal': LeaveBulkActionModal,
  },

  props: {
    myLeaveList: {
      type: Boolean,
      default: false,
    },
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
        {name: 'comment', title: 'Comments', style: {flex: '5%'}},
        {
          name: 'action',
          slot: 'footer',
          title: 'Actions',
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
          style: {
            flex: this.myLeaveList ? '10%' : '20%',
          },
        },
      ],
      checkedItems: [],
      showCommentModal: false,
      commentModalState: null,
      bulkActionModalState: null,
    };
  },

  setup(props) {
    const filters = ref({...defaultFilters});

    const rules = {
      fromDate: [required],
      toDate: [
        required,
        validDateFormat(),
        endDateShouldBeAfterStartDate(
          () => filters.value.fromDate,
          'To date should be after from date',
          {allowSameDate: true},
        ),
      ],
      statuses: [required],
    };

    const serializedFilters = computed(() => {
      const statuses = Array.isArray(filters.value.statuses)
        ? filters.value.statuses
        : [];

      return {
        empNumber: filters.value.employee?.id,
        fromDate: filters.value.fromDate,
        toDate: filters.value.toDate,
        subunitId: filters.value.subunit?.id,
        includeEmployees: filters.value.includePastEmps
          ? 'currentAndPast'
          : 'onlyCurrent',
        statuses: statuses.map(item => item.key),
      };
    });

    const leaveBulkActions = computed(() => {
      const isCancellable =
        serializedFilters.value.statuses[0] === 'taken' ||
        serializedFilters.value.statuses[0] === 'scheduled';
      const isApprovable =
        serializedFilters.value.statuses[0] === 'pendingApproval';
      if (isApprovable || isCancellable) {
        return {
          APPROVE: !props.myLeaveList && isApprovable,
          REJECT: !props.myLeaveList && isApprovable,
          CANCEL: isApprovable || isCancellable,
        };
      }
      return null;
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/leave/${
        props.myLeaveList ? 'leave-requests' : 'employees/leave-requests'
      }`,
    );

    const {
      leaveActions,
      processLeaveRequestAction,
      processLeaveRequestBulkAction,
    } = useLeaveActions(http);

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
      rules,
      filters,
      leaveActions,
      leaveBulkActions,
      processLeaveRequestAction,
      processLeaveRequestBulkAction,
    };
  },

  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};
      const {approve, reject, cancel, more} = this.leaveActions;
      const dropdownActions = [
        {label: 'Add Comment', context: 'add_comment'},
        {label: 'View Leave Details', context: 'leave_details'},
        {label: 'View PIM Info', context: 'pim_details'},
      ];

      row.actions.map(item => {
        if (item.action === 'APPROVE') {
          approve.props.onClick = () => this.onLeaveAction(row.id, 'APPROVE');
          cellConfig.approve = approve;
        }
        if (item.action === 'REJECT') {
          reject.props.onClick = () => this.onLeaveAction(row.id, 'REJECT');
          cellConfig.reject = reject;
        }
        if (item.action === 'CANCEL') {
          if (this.myLeaveList) {
            cancel.props.onClick = () => this.onLeaveAction(row.id, 'CANCEL');
            cellConfig.reject = cancel;
          } else {
            dropdownActions.push({
              label: 'Cancel Leave',
              context: 'cancel_leave',
            });
          }
        }
      });

      more.props.options = dropdownActions;
      more.props.onClick = $event => this.onLeaveDropdownAction($event, row);
      cellConfig.more = more;

      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    onLeaveDropdownAction(event, item) {
      switch (event.context) {
        case 'add_comment':
          this.commentModalState = item.id;
          this.showCommentModal = true;
          break;
        case 'cancel_leave':
          this.onLeaveAction(item.id, 'CANCEL');
          break;
        case 'pim_details':
          navigate('/pim/viewPersonalDetails/empNumber/{id}', {
            id: item.empNumber,
          });
          break;
        default:
          navigate(
            '/leave/viewLeaveRequest/{id}',
            {id: item.id},
            this.myLeaveList && {mode: 'my-leave'},
          );
      }
    },
    onLeaveAction(id, actionType) {
      this.isLoading = true;
      this.processLeaveRequestAction(id, actionType)
        .then(() => {
          this.$toast.updateSuccess();
        })
        .finally(this.resetDataTable);
    },
    async onLeaveActionBulk(actionType) {
      this.isLoading = true;
      this.bulkActionModalState = {
        count: this.checkedItems.length,
        action: actionType,
      };

      const action =
        actionType === 'APPROVE'
          ? 'Approved'
          : actionType === 'REJECT'
          ? 'Rejected'
          : 'Cancelled';
      const ids = this.checkedItems.map(index => {
        return this.items.data[index].id;
      });
      const confirmation = await this.$refs.bulkActionModal.showDialog();

      if (confirmation !== 'ok') {
        this.isLoading = false;
        return;
      }

      this.processLeaveRequestBulkAction(ids, actionType)
        .then(response => {
          const {data} = response.data;
          if (Array.isArray(data))
            this.$toast.success({
              title: 'Success',
              message: `${data.length} Leave Request(s) ${action}`,
            });
        })
        .finally(() => {
          this.bulkActionModalState = null;
          this.resetDataTable();
        });
    },
    onCommentModalClose() {
      this.commentModalState = null;
      this.showCommentModal = false;
      this.resetDataTable();
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    async filterItems() {
      await this.execQuery();
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({method: 'GET', url: 'api/v2/leave/leave-periods'})
      .then(response => {
        const {data} = response.data;
        this.filters.fromDate = data[0]?.startDate;
        this.filters.toDate = data[0]?.endDate;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
}
</style>
