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
          v-if="type"
          class="--help"
          name="question-circle"
          :withContainer="false"
          @click="onModalOpen"
        />
      </div>
    </template>
    <oxd-text class="orangehrm-leave-balance-text" tag="p">
      {{ leaveBalance }}
    </oxd-text>
  </oxd-input-group>
  <leave-balance-modal
    v-if="showModal"
    :data="data"
    @close="onModalClose"
  ></leave-balance-modal>
</template>

<script>
import {toRefs, reactive, computed, watchEffect} from 'vue';
import {APIService} from '@orangehrm/core/util/services/api.service';
import Label from '@orangehrm/oxd/core/components/Label/Label';
import LeaveBalanceModal from '@/orangehrmLeavePlugin/components/LeaveBalanceModal';

export default {
  name: 'leave-balance',
  inheritAttrs: false,
  props: {
    employeeId: {
      type: Number,
    },
    type: {
      type: Object,
    },
    fromDate: {
      type: String,
    },
    toDate: {
      type: String,
    },
  },
  components: {
    'oxd-label': Label,
    'leave-balance-modal': LeaveBalanceModal,
  },
  setup(props) {
    const state = reactive({
      data: null,
      balance: 0,
      showModal: false,
    });
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/leave/my-leave-entitlement',
    );

    const leaveBalance = computed(() => {
      return `${state.balance.toFixed(2)} Day(s)`;
    });

    const onModalOpen = () => {
      state.showModal = true;
    };

    const onModalClose = () => {
      state.showModal = false;
    };

    watchEffect(async () => {
      if (props.type?.id) {
        http
          .getAll({
            id: props.employeeId,
            type: props.type.id,
            fromDate: props.fromDate,
            toDate: props.toDate,
          })
          .then(response => {
            const {data} = response.data;
            if (Array.isArray(data) && data.length > 0) {
              state.data = data[0];
              state.balance = data[0].leaveBalance.balance;
            }
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
}
</style>
