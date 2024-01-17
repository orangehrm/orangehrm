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
  <oxd-divider class="orangehrm-horizontal-margin" />
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <div class="orangehrm-action-header">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('claim.expenses') }}
      </oxd-text>
      <oxd-button
        v-if="canEdit"
        :label="$t('general.add')"
        icon-name="plus"
        display-type="text"
        @click="onClickAdd"
      />
    </div>
  </div>
  <table-header
    :total="total"
    :loading="isLoading"
    :selected="checkedItems.length"
    @delete="onClickDeleteSelected"
  />
  <div class="orangehrm-container">
    <oxd-card-table
      v-model:selected="checkedItems"
      :items="items.data"
      :headers="tableHeaders"
      :selectable="canEdit"
      :clickable="false"
      :loading="isLoading"
      row-decorator="oxd-table-decorator-card"
    />
  </div>
  <div class="orangehrm-bottom-container">
    <oxd-text>{{
      $t('claim.total_amount', {
        currencyName: currency.name,
        totalAmount: formatedAmount,
      })
    }}</oxd-text>
  </div>
  <add-expense-modal
    v-if="showAddExpenseModal"
    :request-id="requestId"
    @close="onCloseAddExpenseModal"
  ></add-expense-modal>
  <edit-expense-modal
    v-if="showEditExpenseModal"
    :request-id="requestId"
    :data="editModalState"
    @close="onCloseEditExpenseModal"
  ></edit-expense-modal>
  <delete-confirmation ref="deleteDialog"></delete-confirmation>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import {computed} from 'vue';
import AddExpenseModal from './AddExpenseModal.vue';
import EditExpenseModal from './EditExpenseModal.vue';
import useLocale from '@/core/util/composable/useLocale';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';

export default {
  name: 'ClaimExpenses',

  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'add-expense-modal': AddExpenseModal,
    'edit-expense-modal': EditExpenseModal,
  },
  props: {
    requestId: {
      type: Number,
      required: true,
    },
    currency: {
      type: Object,
      required: true,
    },
    canEdit: {
      type: Boolean,
      required: true,
    },
  },
  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/claim/requests/${props.requestId}/expenses`,
    );

    const expenseDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          date: item.date
            ? formatDate(parseDate(item.date), jsDateFormat, {locale})
            : '',
          amount: item.amount ? item.amount.toFixed(2) : '0.00',
          note: item.note ? item.note : '',
          expenseType: item.expenseType ? item.expenseType.name : '',
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
    } = usePaginate(http, {
      normalizer: expenseDataNormalizer,
      toastNoRecords: false,
    });

    const totalAmount = computed(() => {
      const meta = response.value?.meta;
      return meta ? meta.totalAmount.toFixed(2) : (0.0).toFixed(2);
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
      totalAmount,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'expenseType',
          slot: 'title',
          title: this.$t('claim.expense_type'),
          style: {flex: 2},
        },
        {
          name: 'date',
          title: this.$t('general.date'),
          style: {flex: 1},
        },
        {
          name: 'note',
          title: this.$t('general.note'),
          cellType: 'oxd-table-cell-truncate',
          style: {flex: 2},
        },
        {
          name: 'amount',
          style: {flex: 1},
          title: this.$t('general.amount'),
        },
      ],
      checkedItems: [],
      showAddExpenseModal: false,
      showEditExpenseModal: false,
      editModalState: null,
    };
  },

  computed: {
    tableHeaders() {
      let computedHeaders = this.headers;
      const headerActions = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {},
      };
      if (this.canEdit) {
        headerActions.cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
        headerActions.cellConfig.delete = {
          onClick: this.onClickDelete,
          component: 'oxd-icon-button',
          props: {
            name: 'trash',
          },
        };
      }

      computedHeaders[3] = {
        name: 'amount',
        title: `${this.$t('general.amount')} (${this.currency.name})`,
        style: {flex: 1},
      };
      if (Object.keys(headerActions.cellConfig).length > 0) {
        computedHeaders.push(headerActions);
      }

      return computedHeaders;
    },
    formatedAmount() {
      const amount = Number(this.totalAmount);
      return amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },

  methods: {
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    onClickAdd() {
      this.showAddExpenseModal = true;
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach((index) => {
        ids.push(this.items?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
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
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    onCloseAddExpenseModal() {
      this.showAddExpenseModal = false;
      this.resetDataTable();
    },
    onCloseEditExpenseModal() {
      this.showEditExpenseModal = false;
      this.resetDataTable();
    },
    onClickEdit(item) {
      this.showEditExpenseModal = true;
      this.editModalState = item;
      this.showAddExpenseModal = false;
    },
  },
};
</script>

<style scoped lang="scss">
.oxd-divider {
  margin-top: 0;
  margin-bottom: 0;
}
.orangehrm-attachment {
  border-bottom-right-radius: 1.2rem;
  overflow: hidden;
}
.orangehrm-action {
  &-header {
    display: flex;
    overflow-wrap: break-word;
    align-items: center;
    button {
      margin-left: 1rem;
      white-space: nowrap;
    }
  }
}
</style>
