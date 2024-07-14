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
  <save-pay-currency
    v-if="showSaveModal"
    :pay-grade-id="payGradeId"
    @close="onSaveModalClose"
  ></save-pay-currency>
  <edit-pay-currency
    v-if="showEditModal"
    :data="editModalState"
    :pay-grade-id="payGradeId"
    @close="onEditModalClose"
  ></edit-pay-currency>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <inline-action-button display-type="secondary" @click="onClickAdd">
          {{ $t('general.currencies') }}
        </inline-action-button>
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:selected="checkedItems"
          :headers="headers"
          :items="items?.data"
          :selectable="selectable"
          :clickable="false"
          :loading="isLoading"
          row-decorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          v-model:current="currentPage"
          :length="pages"
        />
      </div>
      <delete-confirmation ref="deleteDialog"></delete-confirmation>
    </div>
  </div>
</template>
<script>
import InlineActionButton from '@/orangehrmAdminPlugin/components/InlineActionButton.vue';
import {APIService} from '@ohrm/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import SavePayCurrency from '@/orangehrmAdminPlugin/pages/payGrade/SavePayCurrency.vue';
import EditPayCurrency from '@/orangehrmAdminPlugin/pages/payGrade/EditPayCurrency.vue';
import DeleteConfirmationDialog from '@/core/components/dialogs/DeleteConfirmationDialog';

const PayGradeCurrencyNormalizer = (data) => {
  return data.map((item) => {
    let maxSalary = item.maxSalary ? Number(item.maxSalary) : 0;
    let minSalary = item.minSalary ? Number(item.minSalary) : 0;
    maxSalary = maxSalary.toLocaleString('en-US', {
      maximumFractionDigits: 2,
      minimumFractionDigits: 2,
    });
    minSalary = minSalary.toLocaleString('en-US', {
      maximumFractionDigits: 2,
      minimumFractionDigits: 2,
    });

    return {
      id: item.currencyType.id,
      name: item.currencyType.name,
      maxSalary: maxSalary,
      minSalary: minSalary,
    };
  });
};

export default {
  name: 'PayGradeCurrency',
  components: {
    'inline-action-button': InlineActionButton,
    'save-pay-currency': SavePayCurrency,
    'edit-pay-currency': EditPayCurrency,
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    payGradeId: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/admin/pay-grades/${props.payGradeId}/currencies`,
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
      normalizer: PayGradeCurrencyNormalizer,
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
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'name',
          slot: 'title',
          title: this.$t('general.currency'),
          style: {flex: 2},
        },
        {
          name: 'minSalary',
          title: this.$t('admin.minimum_salary'),
          style: {flex: 1},
        },
        {
          name: 'maxSalary',
          title: this.$t('admin.maximum_salary'),
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ],
      checkedItems: [],
      showSaveModal: false,
      showEditModal: false,
      editModalState: null,
    };
  },

  computed: {
    selectable() {
      return !(this.showSaveModal || this.showEditModal);
    },
  },

  methods: {
    onClickAdd() {
      this.showEditModal = false;
      this.editModalState = null;
      this.showSaveModal = true;
      this.checkedItems = [];
    },
    onClickDelete(item) {
      if (!this.selectable) return;
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      if (!this.selectable) return;
      const ids = this.checkedItems.map((index) => {
        return this.items?.data[index].id;
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
    onSaveModalClose() {
      this.showSaveModal = false;
      this.resetDataTable();
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    onClickEdit(item) {
      this.showSaveModal = false;
      this.editModalState = item;
      this.showEditModal = true;
      this.checkedItems = [];
    },
    onEditModalClose() {
      this.showEditModal = false;
      this.editModalState = null;
      this.resetDataTable();
    },
  },
};
</script>
