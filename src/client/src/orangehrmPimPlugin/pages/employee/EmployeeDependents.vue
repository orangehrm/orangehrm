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
  <edit-employee-layout :employee-id="empNumber" screen="dependents">
    <save-dependent
      v-if="showSaveModal"
      :http="http"
      @close="onSaveModalClose"
    ></save-dependent>
    <edit-dependent
      v-if="showEditModal"
      :http="http"
      :data="editModalState"
      @close="onEditModalClose"
    ></edit-dependent>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <profile-action-header @click="onClickAdd">
        {{ $t('pim.assigned_dependents') }}
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
import SaveDependent from '@/orangehrmPimPlugin/components/SaveDependent';
import EditDependent from '@/orangehrmPimPlugin/components/EditDependent';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  components: {
    'profile-action-header': ProfileActionHeader,
    'edit-employee-layout': EditEmployeeLayout,
    'save-dependent': SaveDependent,
    'edit-dependent': EditDependent,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    countries: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.empNumber}/dependents`,
    );
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const dependentNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          name: item.name,
          dateOfBirth: formatDate(parseDate(item.dateOfBirth), jsDateFormat, {
            locale,
          }),
          relationship:
            item.relationshipType == 'other' ? item.relationship : 'Child',
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
      normalizer: dependentNormalizer,
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
          title: this.$t('general.name'),
          style: {flex: 1},
        },
        {
          name: 'relationship',
          title: this.$t('pim.relationship'),
          style: {flex: 1},
        },
        {
          name: 'dateOfBirth',
          title: this.$t('pim.date_of_birth'),
          style: {flex: 1},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: '0.5'},
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
