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
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{
            myLeaveRequest
              ? $t('leave.my_leave_request_details')
              : $t('leave.leave_request_details')
          }}
        </oxd-text>
      </div>
      <oxd-divider
        class="orangehrm-horizontal-margin orangehrm-clear-margins"
      />
      <br />
      <div class="orangehrm-horizontal-padding">
        <oxd-grid :cols="3">
          <oxd-grid-item>
            <oxd-input-group :label="$t('general.employee_name')">
              <oxd-text class="orangehrm-request-details-text" tag="p">
                {{ employeeName }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-group :label="$t('leave.requested_for')">
              <oxd-text class="orangehrm-request-details-text" tag="p">
                {{ leavePeriod }}
              </oxd-text>
            </oxd-input-group>
          </oxd-grid-item>
        </oxd-grid>
      </div>
      <br />
      <table-header
        :selected="0"
        :total="total"
        :loading="isLoading"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="response && response.data"
          :selectable="false"
          :clickable="false"
          :loading="isLoading"
          row-decorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <span>
          <oxd-button
            display-type="ghost"
            :label="$t('general.back')"
            @click="onClickBack"
          />
          <oxd-button
            class="orangehrm-left-space"
            display-type="secondary"
            icon-name="chat-right-text-fill"
            :label="$t('general.comments')"
            @click="onClickComments"
          />
        </span>
        <oxd-pagination
          v-if="showPaginator"
          v-model:current="currentPage"
          :length="pages"
        />
      </div>
    </div>
  </div>
  <leave-comment-modal
    v-if="showCommentModal"
    :id="commentModalState"
    :leave-request="isLeaveRequest"
    @close="onCommentModalClose"
  >
  </leave-comment-modal>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {truncate} from '@ohrm/core/util/helper/truncate';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useLeaveActions from '@/orangehrmLeavePlugin/util/composable/useLeaveActions';
import LeaveCommentsModal from '@/orangehrmLeavePlugin/components/LeaveCommentsModal';
import usei18n from '@/core/util/composable/usei18n';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'LeaveViewRequest',

  components: {
    'leave-comment-modal': LeaveCommentsModal,
  },

  props: {
    leaveRequestId: {
      type: String,
      required: true,
    },
    myLeaveRequest: {
      type: Boolean,
      default: false,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/leave/leave-requests/${props.leaveRequestId}/leaves`,
    );

    const {leaveActions, processLeaveAction} = useLeaveActions(http);
    const {$t} = usei18n();
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();
    const {$tEmpName} = useEmployeeNameTranslate();

    const leaveRequestNormalizer = (data) => {
      return data.map((item) => {
        let leaveDatePeriod = '';
        const duration = item.dates.durationType?.type;

        if (item.dates.fromDate) {
          leaveDatePeriod = formatDate(
            parseDate(item.dates.fromDate),
            jsDateFormat,
            {locale},
          );
        }
        if (item.dates.startTime && item.dates.endTime) {
          leaveDatePeriod += ` (${item.dates.startTime} - ${item.dates.endTime})`;
        }
        if (
          duration === 'half_day_morning' ||
          duration === 'half_day_afternoon'
        ) {
          leaveDatePeriod += ` ${$t('leave.half_day')}`;
        }

        const leaveTypeName = item.leaveType?.name;
        if (item.leaveType?.deleted) {
          leaveTypeName + $t('general.deleted');
        }

        return {
          id: item.id,
          date: leaveDatePeriod,
          leaveType: leaveTypeName,
          leaveBalance: item.leaveBalance?.balance.balance
            ? parseFloat(item.leaveBalance.balance.balance).toFixed(2)
            : undefined,
          duration: parseFloat(item.lengthHours).toFixed(2),
          status: item.leaveStatus?.name,
          comment: truncate(item.lastComment?.comment),
          actions: item.allowedActions,
          canComment: !(
            item.leaveStatus?.id === 5 || item.leaveStatus?.id === 4
          ),
        };
      });
    };

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {normalizer: leaveRequestNormalizer});

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      response,
      leaveActions,
      processLeaveAction,
      jsDateFormat,
      locale,
      translateEmpName: $tEmpName,
    };
  },

  data() {
    return {
      headers: [
        {name: 'date', title: this.$t('general.date'), style: {flex: 1}},
        {
          name: 'leaveType',
          title: this.$t('leave.leave_type'),
          style: {flex: 1},
        },
        {
          name: 'leaveBalance',
          title: this.$t('leave.leave_balance_days'),
          style: {flex: 1},
        },
        {
          name: 'duration',
          title: this.$t('attendance.duration_hours'),
          style: {flex: 1},
        },
        {name: 'status', title: this.$t('general.status'), style: {flex: 1}},
        {
          name: 'comment',
          title: this.$t('general.comments'),
          style: {flex: '10%'},
        },
        {
          name: 'action',
          slot: 'footer',
          title: this.$t('general.actions'),
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
          style: {flex: '20%'},
        },
      ],
      showCommentModal: false,
      commentModalState: null,
      isLeaveRequest: false,
    };
  },

  computed: {
    employeeName() {
      const employee = this.response?.meta?.employee;
      if (employee) {
        return this.translateEmpName(employee, {
          includeMiddle: true,
          excludePastEmpTag: false,
        });
      }
      return '';
    },
    leavePeriod() {
      const startDate = formatDate(
        parseDate(this.response?.meta?.startDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
      const endDate = formatDate(
        parseDate(this.response?.meta?.endDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
      return startDate === endDate ? startDate : `${startDate} - ${endDate}`;
    },
  },

  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};
      const dropdownActions = [];
      const {approve, reject, cancel, more} = this.leaveActions;

      if (row.canComment) {
        dropdownActions.push({
          label: 'Add Comment',
          context: 'add_comment',
        });
      }

      row.actions.map((item) => {
        if (item.action === 'APPROVE') {
          approve.props.label = this.$t('general.approve');
          approve.props.onClick = () => this.onLeaveAction(row.id, 'APPROVE');
          cellConfig.approve = approve;
        }
        if (item.action === 'REJECT') {
          reject.props.label = this.$t('general.reject');
          reject.props.onClick = () => this.onLeaveAction(row.id, 'REJECT');
          cellConfig.reject = reject;
        }
        if (item.action === 'CANCEL') {
          if (this.myLeaveRequest) {
            cancel.props.label = this.$t('general.cancel');
            cancel.props.onClick = () => this.onLeaveAction(row.id, 'CANCEL');
            cellConfig.cancel = cancel;
          } else {
            dropdownActions.push({
              label: 'Cancel Leave',
              context: 'cancel_leave',
            });
          }
        }
      });

      if (dropdownActions.length > 0) {
        more.props.options = dropdownActions;
        more.props.onClick = ($event) =>
          this.onLeaveDropdownAction($event, row);
        cellConfig.more = more;
      }

      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    onClickComments() {
      this.commentModalState = this.leaveRequestId;
      this.isLeaveRequest = true;
      this.showCommentModal = true;
    },
    onCommentModalClose() {
      this.commentModalState = null;
      this.showCommentModal = false;
      this.resetDataTable();
    },
    onLeaveDropdownAction(event, item) {
      if (event.context === 'cancel_leave') {
        this.onLeaveAction(item.id, 'CANCEL');
      } else {
        this.commentModalState = item.id;
        this.isLeaveRequest = false;
        this.showCommentModal = true;
      }
    },
    onLeaveAction(id, actionType) {
      this.isLoading = true;
      this.processLeaveAction(id, actionType)
        .then(() => {
          this.$toast.updateSuccess();
        })
        .finally(this.resetDataTable);
    },
    onClickBack() {
      this.myLeaveRequest
        ? navigate('/leave/viewMyLeaveList')
        : navigate('/leave/viewLeaveList');
    },
    async resetDataTable() {
      await this.execQuery();
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-bottom-container {
  align-items: center;
  justify-content: space-between;
}
.orangehrm-request-details-text {
  font-size: $oxd-input-control-font-size;
}
::v-deep(.card-footer-slot) {
  .oxd-table-cell-actions {
    justify-content: flex-end;
  }
  .oxd-table-cell-actions > * {
    margin: 0 !important;
  }
}
</style>
