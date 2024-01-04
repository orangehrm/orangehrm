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
  <div>
    <save-license
      v-if="showSaveModal"
      :http="http"
      :api="licenceEndpoint"
      @close="onSaveModalClose"
    ></save-license>
    <edit-license
      v-if="showEditModal"
      :http="http"
      :data="editModalState"
      @close="onEditModalClose"
    ></edit-license>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <profile-action-header @click="onClickAdd">
        {{ $t('pim.license') }}
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
        :clickable="false"
        :loading="isLoading"
        :disabled="isDisabled"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div v-if="showPaginator" class="orangehrm-bottom-container">
      <oxd-pagination v-model:current="currentPage" :length="pages" />
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@ohrm/core/util/services/api.service';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader';
import SaveLicense from '@/orangehrmPimPlugin/components/SaveLicense';
import EditLicense from '@/orangehrmPimPlugin/components/EditLicense';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  name: 'EmployeeLicense',

  components: {
    'profile-action-header': ProfileActionHeader,
    'save-license': SaveLicense,
    'edit-license': EditLicense,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    employeeId: {
      type: String,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.employeeId}/licenses`,
    );
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const licenceEndpoint = `/api/v2/pim/employees/${props.employeeId}/licenses/allowed`;

    const licenseNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.license.id,
          type: item.license.name,
          licenseNo: item.licenseNo,
          issuedDate: formatDate(parseDate(item.issuedDate), jsDateFormat, {
            locale,
          }),
          expiryDate: formatDate(parseDate(item.expiryDate), jsDateFormat, {
            locale,
          }),
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
      normalizer: licenseNormalizer,
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
      licenceEndpoint,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'type',
          slot: 'title',
          title: this.$t('pim.license_type'),
          style: {flex: 1},
        },
        {
          name: 'issuedDate',
          title: this.$t('pim.issued_date'),
          style: {flex: 1},
        },
        {
          name: 'expiryDate',
          title: this.$t('general.expiry_date'),
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {'flex-basis': '10em'},
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
