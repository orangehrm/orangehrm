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
  <edit-employee-layout :employee-id="empNumber" screen="salary">
    <save-salary-component
      v-if="showSaveModal"
      :http="http"
      :paygrades="paygrades"
      :pay-frequencies="payFrequencies"
      :currencies="currencies"
      :account-types="accountTypes"
      @close="onSaveModalClose"
    ></save-salary-component>
    <edit-salary-component
      v-if="showEditModal"
      :http="http"
      :data="editModalState"
      :paygrades="paygrades"
      :pay-frequencies="payFrequencies"
      :currencies="currencies"
      :account-types="accountTypes"
      @close="onEditModalClose"
    ></edit-salary-component>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <profile-action-header
        :action-button-shown="$can.update(`salary_details`)"
        @click="onClickAdd"
      >
        {{ $t('pim.assigned_salary_components') }}
      </profile-action-header>
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
        :headers="tableHeaders"
        :items="items?.data"
        :selectable="$can.delete(`salary_details`)"
        :disabled="isDisabled"
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
  </edit-employee-layout>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@ohrm/core/util/services/api.service';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import SaveSalaryComponent from '@/orangehrmPimPlugin/components/SaveSalaryComponent';
import EditSalaryComponent from '@/orangehrmPimPlugin/components/EditSalaryComponent';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

const salaryNormalizer = (data) => {
  return data.map((item) => {
    return {
      id: item.id,
      name: item.salaryName,
      amount: item.amount,
      currency: item.currencyType?.name,
      frequency: item.payPeriod?.name,
      depositAmount: item.directDebit?.amount,
    };
  });
};

export default {
  components: {
    'profile-action-header': ProfileActionHeader,
    'edit-employee-layout': EditEmployeeLayout,
    'save-salary-component': SaveSalaryComponent,
    'edit-salary-component': EditSalaryComponent,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    paygrades: {
      type: Array,
      default: () => [],
    },
    payFrequencies: {
      type: Array,
      default: () => [],
    },
    currencies: {
      type: Array,
      default: () => [],
    },
    accountTypes: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.empNumber}/salary-components`,
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
      normalizer: salaryNormalizer,
      toastNoRecords: false,
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
          title: this.$t('pim.salary_component'),
          style: {flex: 1},
        },
        {name: 'amount', title: this.$t('general.amount'), style: {flex: 1}},
        {
          name: 'currency',
          title: this.$t('general.currency'),
          style: {flex: 1},
        },
        {
          name: 'frequency',
          title: this.$t('pim.pay_frequency'),
          style: {flex: 1},
        },
        {
          name: 'depositAmount',
          title: this.$t('pim.direct_deposit_amount'),
          style: {flex: 1},
        },
      ],
      checkedItems: [],
      showSaveModal: false,
      showEditModal: false,
      editModalState: null,
    };
  },

  computed: {
    isDisabled() {
      return this.showSaveModal || this.showEditModal;
    },
    tableHeaders() {
      const headerActions = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {},
      };
      if (this.$can.delete(`salary_details`)) {
        headerActions.cellConfig.delete = {
          onClick: this.onClickDelete,
          component: 'oxd-icon-button',
          props: {
            name: 'trash',
          },
        };
      }
      if (this.$can.update(`salary_details`)) {
        headerActions.cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
      }
      return Object.keys(headerActions.cellConfig).length > 0
        ? this.headers.concat([headerActions])
        : this.headers;
    },
  },

  methods: {
    onClickDeleteSelected() {
      const ids = this.checkedItems.map((index) => {
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
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
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    onClickAdd() {
      this.showEditModal = false;
      this.editModalState = null;
      this.showSaveModal = true;
    },
    onClickEdit(item) {
      this.showSaveModal = false;
      this.editModalState = item;
      this.showEditModal = true;
    },
    onSaveModalClose() {
      this.showSaveModal = false;
      this.resetDataTable();
    },
    onEditModalClose() {
      this.showEditModal = false;
      this.editModalState = null;
      this.resetDataTable();
    },
  },
};
</script>
