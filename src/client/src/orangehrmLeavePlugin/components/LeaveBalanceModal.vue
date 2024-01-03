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
  <oxd-dialog
    :gutters="false"
    class="orangehrm-dialog-modal"
    @update:show="onCancel"
  >
    <div class="orangehrm-dialog-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('leave.leave_balance_details') }}
      </oxd-text>
      <oxd-text type="subtitle-2">
        {{ $t('leave.as_of_date') }} - {{ asAtDate }}
      </oxd-text>
    </div>
    <oxd-divider
      class="orangehrm-dialog-horizontal-margin orangehrm-clear-margins"
    />
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-grid :cols="3">
        <oxd-grid-item>
          <oxd-input-group :label="$t('general.employee_name')">
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ employeeName }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group
            class="--offset-column-1"
            :label="$t('leave.leave_type')"
          >
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ leaveType }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group :label="$t('leave.total_entitlement')">
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ totalEntitlement }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group :label="$t('leave.balance')">
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ leaveBalance }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
      </oxd-grid>
    </div>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items"
        :clickable="false"
        class="orangehrm-horizontal-padding"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-form-actions>
        <oxd-button
          type="submit"
          display-type="secondary"
          :label="$t('general.ok')"
          @click="onCancel"
        />
      </oxd-form-actions>
    </div>
  </oxd-dialog>
</template>

<script>
import {OxdDialog} from '@ohrm/oxd';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  name: 'LeaveBalanceModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    data: {
      type: Object,
      default: () => null,
    },
    meta: {
      type: Object,
      default: () => null,
    },
  },
  emits: ['close'],
  setup() {
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    return {
      locale,
      jsDateFormat,
    };
  },
  data() {
    return {
      headers: [
        {
          title: this.$t('leave.leave_status'),
          name: 'status',
          slot: 'left',
          style: {flex: 1},
        },
        {
          title: this.$t('leave.days'),
          name: 'days',
          slot: 'right',
          style: {
            flex: 1,
            textAlign: 'right',
            justifyContent: 'flex-end',
          },
        },
      ],
    };
  },
  computed: {
    items() {
      if (this.data) {
        const {taken, scheduled, pending} = this.data;
        return [
          {status: this.$t('leave.taken'), days: taken.toFixed(2)},
          {status: this.$t('leave.scheduled'), days: scheduled.toFixed(2)},
          {status: this.$t('leave.pending_approval'), days: pending.toFixed(2)},
        ];
      }
      return [];
    },
    asAtDate() {
      return formatDate(parseDate(this.data?.asAtDate), this.jsDateFormat, {
        locale: this.locale,
      });
    },
    leaveType() {
      return this.meta?.leaveType?.name;
    },
    employeeName() {
      const employee = this.meta?.employee;
      if (employee) {
        return `${employee.firstName} ${employee.lastName}
          ${employee.terminationId ? this.$t('general.past_employee') : ''}`;
      }
      return '';
    },
    totalEntitlement() {
      return this.data?.entitled
        ? `${parseFloat(this.data.entitled).toFixed(2)} Day(s)`
        : '0.00 Day(s)';
    },
    leaveBalance() {
      return this.data?.balance
        ? `${parseFloat(this.data.balance).toFixed(2)} Day(s)`
        : '0.00 Day(s)';
    },
  },
  methods: {
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>

<style src="./leave-balance-modal.scss" lang="scss" scoped></style>
