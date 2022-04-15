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
  <div>
    <save-language
      v-if="showSaveModal"
      :http="http"
      :api="languagesEndpoint"
      :fluencies="fluencies"
      :competencies="competencies"
      @close="onSaveModalClose"
    ></save-language>
    <edit-language
      v-if="showEditModal"
      :http="http"
      :data="editModalState"
      :employee-id="employeeId"
      :competencies="competencies"
      @close="onEditModalClose"
    ></edit-language>
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <profile-action-header @click="onClickAdd">
        {{ $t('general.languages') }}
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
        :disabled="isDisabled"
        :loading="isLoading"
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
import SaveLanguage from '@/orangehrmPimPlugin/components/SaveLanguage';
import EditLanguage from '@/orangehrmPimPlugin/components/EditLanguage';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

const languageNormalizer = data => {
  return data.map(item => {
    return {
      language: item.language.name,
      languageId: item.language.id,
      fluency: item.fluency.name,
      fluencyId: item.fluency.id,
      competency: item.competency.name,
      comments: item.comment,
    };
  });
};

export default {
  name: 'EmployeeLanguages',

  components: {
    'profile-action-header': ProfileActionHeader,
    'save-language': SaveLanguage,
    'edit-language': EditLanguage,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    employeeId: {
      type: String,
      required: true,
    },
    fluencies: {
      type: Array,
      required: true,
    },
    competencies: {
      type: Array,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `api/v2/pim/employees/${props.employeeId}/languages`,
    );

    const languagesEndpoint = `api/v2/pim/employees/${props.employeeId}/languages/allowed?limit=0`;

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
      normalizer: languageNormalizer,
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
      languagesEndpoint,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'language',
          slot: 'title',
          title: this.$t('general.language'),
          style: {flex: 1},
        },
        {name: 'fluency', title: this.$t('pim.fluency'), style: {flex: 1}},
        {
          name: 'competency',
          title: this.$t('pim.competency'),
          style: {flex: 1},
        },
        {
          name: 'comments',
          title: this.$t('general.comments'),
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
      const ids = this.checkedItems.map(index => {
        return {
          languageId: this.items?.data[index].languageId,
          fluencyId: this.items?.data[index].fluencyId,
        };
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems([
            {
              languageId: item.languageId,
              fluencyId: item.fluencyId,
            },
          ]);
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
