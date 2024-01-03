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
  <edit-employee-layout :employee-id="empNumber" screen="membership">
    <save-membership
      v-if="showSaveModal"
      :http="http"
      :currencies="currencies"
      :paid-by="paidBy"
      :memberships="memberships"
      @close="onSaveModalClose"
    ></save-membership>
    <edit-membership
      v-if="showEditModal"
      :http="http"
      :currencies="currencies"
      :paid-by="paidBy"
      :memberships="memberships"
      :data="editModalState"
      @close="onEditModalClose"
    ></edit-membership>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <profile-action-header @click="onClickAdd">
        {{ $t('pim.assigned_memberships') }}
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
        :headers="headers"
        :items="items?.data"
        :selectable="true"
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
import SaveMembership from '@/orangehrmPimPlugin/components/SaveMembership';
import EditMembership from '@/orangehrmPimPlugin/components/EditMembership';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  components: {
    'profile-action-header': ProfileActionHeader,
    'edit-employee-layout': EditEmployeeLayout,
    'save-membership': SaveMembership,
    'edit-membership': EditMembership,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    currencies: {
      type: Array,
      default: () => [],
    },
    paidBy: {
      type: Array,
      default: () => [],
    },
    memberships: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.empNumber}/memberships`,
    );
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const membershipNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          membershipId: item.membership.id,
          membershipName: item.membership.name,
          subscriptionPaidBy: item.subscriptionPaidBy,
          subscriptionFee: item.subscriptionFee,
          subscriptionTypeId: item.currencyType.id,
          subscriptionCurrencyName: item.currencyType.name,
          subscriptionCommenceDate: formatDate(
            parseDate(item.subscriptionCommenceDate),
            jsDateFormat,
            {locale},
          ),
          subscriptionRenewalDate: formatDate(
            parseDate(item.subscriptionRenewalDate),
            jsDateFormat,
            {locale},
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
    } = usePaginate(http, {
      normalizer: membershipNormalizer,
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
          name: 'membershipName',
          slot: 'title',
          title: this.$t('pim.membership'),
          style: {flex: 1},
        },
        {
          name: 'subscriptionPaidBy',
          title: this.$t('pim.subscription_paid_by'),
          style: {flex: 1},
        },
        {
          name: 'subscriptionFee',
          title: this.$t('pim.subscription_amount'),
          style: {flex: 1},
        },
        {
          name: 'subscriptionCurrencyName',
          title: this.$t('general.currency'),
          style: {flex: 1},
        },
        {
          name: 'subscriptionCommenceDate',
          title: this.$t('pim.subscription_commence_date'),
          style: {flex: 1},
        },
        {
          name: 'subscriptionRenewalDate',
          title: this.$t('pim.subscription_renewal_date'),
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
    isDisabled() {
      return this.showSaveModal || this.showEditModal;
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

<style src="./employee.scss" lang="scss" scoped></style>
