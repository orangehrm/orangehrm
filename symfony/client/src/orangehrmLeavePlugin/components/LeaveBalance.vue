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
  <oxd-input-group>
    <template v-slot:label>
      <div class="orangehrm-leave-balance">
        <oxd-label :label="$t('leave.leave_balance')" />
        <oxd-icon-button
          v-if="leaveData.type"
          class="--help"
          name="question-circle"
          :withContainer="false"
          @click="onModalOpen"
        />
      </div>
    </template>
    <oxd-text v-if="balance >= 0" class="orangehrm-leave-balance-text" tag="p">
      {{ leaveBalance }}
    </oxd-text>
    <oxd-text v-else class="orangehrm-leave-balance-text --error" tag="p">
      {{ $t('leave.balance_not_sufficient') }}
    </oxd-text>
  </oxd-input-group>
  <component
    :is="
      balance >= 0 ? 'leave-balance-modal' : 'leave-balance-insufficient-modal'
    "
    v-if="showModal"
    :data="data"
    :meta="meta"
    @close="onModalClose"
  ></component>
</template>

<script>
import {toRefs, reactive, computed, watchPostEffect} from 'vue';
import {APIService} from '@orangehrm/core/util/services/api.service';
import Label from '@orangehrm/oxd/core/components/Label/Label';
import LeaveBalanceModal from '@/orangehrmLeavePlugin/components/LeaveBalanceModal';
import LeaveBalanceInsufficientModal from '@/orangehrmLeavePlugin/components/LeaveBalanceInsufficientModal';

export default {
  name: 'leave-balance',
  inheritAttrs: false,
  props: {
    leaveData: {
      type: Object,
    },
  },
  components: {
    'oxd-label': Label,
    'leave-balance-modal': LeaveBalanceModal,
    'leave-balance-insufficient-modal': LeaveBalanceInsufficientModal,
  },
  setup(props) {
    const state = reactive({
      data: null,
      meta: null,
      balance: 0,
      showModal: false,
    });
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/leave-balance/leave-type',
    );

    const leaveBalance = computed(() => {
      return props.leaveData.type?.id
        ? `${state.balance.toFixed(2)} Day(s)`
        : '0.00 Day(s)';
    });

    const onModalOpen = () => {
      state.showModal = true;
    };

    const onModalClose = () => {
      state.showModal = false;
    };

    watchPostEffect(async () => {
      const payload = {
        fromDate: props.leaveData.fromDate,
        toDate: props.leaveData.toDate,
        duration: null,
        partialOption: props.leaveData.partialOptions?.key,
        endDuration: null,
        empNumber: props.leaveData.employee?.id,
      };
      if (props.leaveData.duration.type?.key) {
        payload['duration[type]'] = props.leaveData.duration.type.key;
        payload['duration[fromTime]'] =
          props.leaveData.duration.type.id === 4
            ? props.leaveData.duration.fromTime
            : null;
        payload['duration[toTime]'] =
          props.leaveData.duration.type.id === 4
            ? props.leaveData.duration.toTime
            : null;
      }
      if (props.leaveData.endDuration?.type?.key) {
        payload['endDuration[type]'] = props.leaveData.endDuration.type.key;
        payload['endDuration[fromTime]'] =
          props.leaveData.endDuration.type.id === 4
            ? props.leaveData.endDuration.fromTime
            : null;
        payload['endDuration[toTime]'] =
          props.leaveData.endDuration.type.id === 4
            ? props.leaveData.endDuration.toTime
            : null;
      }

      if (props.leaveData.type?.id) {
        http
          .get(props.leaveData.type.id, payload)
          .then(response => {
            if (response.status !== 200) return;
            const {data, meta} = response.data;
            state.meta = meta;
            if (data.balance) {
              // response sends balance directly when no duration defined
              state.data = data.balance;
              state.balance = data.balance?.balance;
            } else if (data.breakdown && data.negative === false) {
              // if duration is defined and the balance is not exceeded
              state.data = data.breakdown[0].balance;
              state.balance = data.breakdown[0].balance?.balance;
            } else if (data.breakdown && data.negative === true) {
              // if duration is defined and the balance is exceeded
              state.data = data.breakdown;
              state.balance = -1;
            } else {
              state.data = null;
              state.balance = 0;
            }
          })
          .catch(() => {
            state.data = null;
            state.balance = 0;
          });
      }
    });

    return {
      ...toRefs(state),
      leaveBalance,
      onModalOpen,
      onModalClose,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-leave-balance {
  display: flex;
  align-items: center;
  & .--help {
    margin-left: 5px;
  }
}
.orangehrm-leave-balance-text {
  padding: $oxd-input-control-vertical-padding 0rem;
  &.--error {
    color: $oxd-feedback-danger-color;
  }
}
</style>
