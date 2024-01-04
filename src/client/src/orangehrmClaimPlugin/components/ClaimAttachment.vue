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
        {{ $t('general.attachments') }}
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
      class="orangehrm-card-table"
      :items="items.data"
      :headers="tableHeaders"
      :selectable="canEdit"
      :clickable="false"
      :loading="isLoading"
      row-decorator="oxd-table-decorator-card"
    />
  </div>
  <add-attachment-modal
    v-if="showAddAttachmentModal"
    :request-id="requestId"
    :allowed-file-types="allowedFileTypes"
    :max-file-size="maxFileSize"
    @close="onCloseAddAttachmentModal"
  ></add-attachment-modal>
  <edit-attachment-modal
    v-if="showEditAttachmentModal"
    :request-id="requestId"
    :allowed-file-types="allowedFileTypes"
    :max-file-size="maxFileSize"
    :data="editModalState"
    @close="onCloseEditAttachmentModal"
  ></edit-attachment-modal>
  <delete-confirmation ref="deleteDialog"></delete-confirmation>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import {convertFilesizeToString} from '@ohrm/core/util/helper/filesize';
import AddAttachmentModal from './AddAttachmentModal.vue';
import EditAttachmentModal from './EditAttachmentModal.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import useLocale from '@/core/util/composable/useLocale';
import {formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';

export default {
  name: 'ClaimAttachment',

  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    'add-attachment-modal': AddAttachmentModal,
    'edit-attachment-modal': EditAttachmentModal,
  },
  props: {
    requestId: {
      type: Number,
      required: true,
    },
    canEdit: {
      type: Boolean,
      required: true,
    },
    allowedFileTypes: {
      type: Array,
      required: true,
    },
    maxFileSize: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/claim/requests/${props.requestId}/attachments`,
    );
    const {$tEmpName} = useEmployeeNameTranslate();

    const attachmentDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          id: item.id,
          attachedDate: item.date
            ? formatDate(parseDate(item.date), jsDateFormat, {locale})
            : '',
          filename: item.attachment.fileName ?? '',
          size: item.attachment.size
            ? convertFilesizeToString(item.attachment.size, 2)
            : '',
          fileType: item.attachment.fileType ? item.attachment.fileType : '',
          description: item.attachment.description
            ? item.attachment.description
            : '',
          attachedByName: $tEmpName(item.attachedBy, {
            excludePastEmpTag: false,
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
          cellType: 'oxd-table-cell-truncate',
          title: this.$t('general.file_name'),
          style: {flex: 1},
        },
        {
          name: 'description',
          title: this.$t('general.description'),
          cellType: 'oxd-table-cell-truncate',
          style: {flex: 1},
        },
        {name: 'size', title: this.$t('general.size'), style: {flex: 1}},
        {name: 'fileType', title: this.$t('general.type'), style: {flex: 1}},
        {
          name: 'attachedDate',
          title: this.$t('pim.date_added'),
          style: {flex: 1},
        },
        {
          name: 'attachedByName',
          title: this.$t('pim.added_by'),
          style: {flex: 1},
        },
      ],
      checkedItems: [],
      editModalState: null,
      showAddAttachmentModal: false,
      showEditAttachmentModal: false,
    };
  },

  computed: {
    tableHeaders() {
      let computedHeaders = this.headers;
      if (computedHeaders.length > 6) {
        computedHeaders.pop();
      }
      const headerActions = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {},
      };
      if (this.canEdit) {
        headerActions.cellConfig.delete = {
          onClick: this.onClickDelete,
          component: 'oxd-icon-button',
          props: {
            name: 'trash',
          },
        };
        headerActions.cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
      }
      headerActions.cellConfig.download = {
        onClick: this.onClickDownload,
        props: {
          name: 'download',
        },
      };
      computedHeaders.push(headerActions);

      return computedHeaders;
    },
  },

  methods: {
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    onClickAdd() {
      this.showAddAttachmentModal = true;
      this.showEditAttachmentModal = false;
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
    onClickEdit(item) {
      this.showAddAttachmentModal = false;
      this.editModalState = item;
      this.showEditAttachmentModal = true;
    },
    onCloseAddAttachmentModal() {
      this.showAddAttachmentModal = false;
      this.reloadAttachments();
    },
    onCloseEditAttachmentModal() {
      this.showEditAttachmentModal = false;
      this.reloadAttachments();
    },
    async reloadAttachments() {
      await this.execQuery();
    },
    onClickDownload(item) {
      const downUrl = `${window.appGlobal.baseUrl}/claim/requests/${this.requestId}/attachId/${item.id}`;
      window.open(downUrl, '_blank');
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
.orangehrm-button-margin {
  margin: 0.25rem;
}
</style>
