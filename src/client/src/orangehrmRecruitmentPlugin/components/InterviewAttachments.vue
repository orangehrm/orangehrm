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
  <div class="orangehrm-attachment orangehrm-card-container">
    <save-attachment
      v-if="showSaveModal"
      :http="http"
      :allowed-file-types="allowedFileTypes"
      @close="closeModel"
    ></save-attachment>
    <edit-attachment
      v-else-if="showEditModal"
      :data="editModalState"
      :http="http"
      :allowed-file-types="allowedFileTypes"
      @close="closeModel"
    ></edit-attachment>
    <template v-else>
      <div
        class="orangehrm-horizontal-padding
        orangehrm-vertical-padding"
      >
        <profile-action-header @click="onClickAdd">
          {{ $t('general.attachments') }}
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
          :clickable="false"
          :loading="isLoading"
          row-decorator="oxd-table-decorator-card"
        />
      </div>
      <div v-if="showPaginator" class="orangehrm-bottom-container">
        <oxd-pagination v-model:current="currentPage" :length="pages" />
      </div>
    </template>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {convertFilesizeToString} from '@ohrm/core/util/helper/filesize';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import SaveAttachment from '@/orangehrmPimPlugin/components/SaveAttachment';
import EditAttachment from '@/orangehrmPimPlugin/components/EditAttachment';

const attachmentDataNormalizer = data => {
  return data.map(item => {
    return {
      ...item,
      size: convertFilesizeToString(item.size, 2),
    };
  });
};

export default {
  name: 'InterviewAttachments',
  components: {
    'profile-action-header': ProfileActionHeader,
    'delete-confirmation': DeleteConfirmationDialog,
    'save-attachment': SaveAttachment,
    'edit-attachment': EditAttachment,
  },
  props: {
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      'https://c81c3149-4936-41d9-ab3d-e25f1bff2934.mock.pstmn.io',
      `recruitment/interviewAttachments/interviewer`,
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
      normalizer: attachmentDataNormalizer,
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
          name: 'filename',
          slot: 'title',
          title: this.$t('general.file_name'),
          style: {flex: 1},
        },
        {name: 'size', title: this.$t('general.size'), style: {flex: 1}},
        {name: 'fileType', title: this.$t('general.type'), style: {flex: 1}},
        {name: 'comment', title: this.$t('general.comment'), style: {flex: 1}},
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            download: {
              onClick: this.onClickDownload,
              props: {
                name: 'download',
              },
            },
          },
        },
      ],
      checkedItems: [],
      isSave: true,
      showSaveModal: false,
      showEditModal: false,
      editModalState: null,
    };
  },

  methods: {
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.items?.data[index].id;
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
      this.showSaveModal = true;
      this.showEditModal = false;
    },
    onClickEdit(item) {
      this.showSaveModal = false;
      this.showEditModal = true;
      this.editModalState = item;
    },
    closeModel() {
      this.showSaveModal = false;
      this.showEditModal = false;
    },
    onClickDownload(item) {
      const downUrl = `${window.appGlobal.baseUrl}/recruitment/resume/${item?.id}`;
      window.open(downUrl, '_blank');
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-card-container {
  padding: 1.2rem 0 !important;
}
</style>
