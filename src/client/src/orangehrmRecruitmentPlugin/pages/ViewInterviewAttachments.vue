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
  <div class="orangehrm-attachment">
    <save-interview-attachment
      v-if="showSaveModal"
      :http="http"
      :max-file-size="maxFileSize"
      :allowed-file-types="allowedFileTypes"
      @close="closeModel"
    ></save-interview-attachment>
    <edit-interview-attachment
      v-else-if="showEditModal"
      :http="http"
      :data="editModalState"
      :max-file-size="maxFileSize"
      :allowed-file-types="allowedFileTypes"
      @close="closeModel"
    ></edit-interview-attachment>
    <template v-else>
      <div class="orangehrm-attachment-header">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('general.attachments') }}
        </oxd-text>
        <oxd-button
          icon-name="plus"
          display-type="text"
          :label="$t('general.add')"
          @click="onClickAdd"
        />
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
          :selectable="true"
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
    </template>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {convertFilesizeToString} from '@ohrm/core/util/helper/filesize';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import SaveInterviewAttachment from '@/orangehrmRecruitmentPlugin/components/SaveInterviewAttachment.vue';
import EditInterviewAttachment from '@/orangehrmRecruitmentPlugin/components/EditInterviewAttachment.vue';

const attachmentDataNormalizer = (data) => {
  return data.map((item) => {
    return {
      id: item.id,
      interviewId: item.interviewId,
      filename: item.attachment?.fileName,
      size: convertFilesizeToString(item.attachment?.fileSize || 0, 2),
      fileType: item.attachment?.fileType,
      comment: item.comment,
    };
  });
};

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'save-interview-attachment': SaveInterviewAttachment,
    'edit-interview-attachment': EditInterviewAttachment,
  },
  props: {
    interviewId: {
      type: Number,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/interviews/${props.interviewId}/attachments`,
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
      showSaveModal: false,
      showEditModal: false,
      editModalState: null,
    };
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
      this.resetDataTable();
    },
    onClickDownload(item) {
      const downUrl = `${window.appGlobal.baseUrl}/recruitment/viewInterviewAttachment/interview/${this.interviewId}/attachment/${item.id}`;
      window.open(downUrl, '_blank');
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-attachment {
  border-radius: 1.2rem;
  background-color: $oxd-white-color;
  &-header {
    display: flex;
    overflow-wrap: break-word;
    align-items: center;
    padding: 25px;
    button {
      margin-left: 1rem;
      white-space: nowrap;
    }
  }
}
</style>
