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
    <oxd-divider />
    <save-attachment
      v-if="showSaveModal"
      :http="http"
      :allowed-file-types="allowedFileTypes"
      :max-file-size="maxFileSize"
      @close="onSaveModalClose"
    ></save-attachment>
    <edit-attachment
      v-else-if="showEditModal"
      :data="editModalState"
      :http="http"
      :allowed-file-types="allowedFileTypes"
      :max-file-size="maxFileSize"
      @close="onEditModalClose"
    ></edit-attachment>
    <template v-else>
      <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
        <profile-action-header
          :action-button-shown="$can.create(`${screen}_attachment`)"
          @click="onClickAdd"
        >
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
          :headers="tableHeaders"
          :items="items?.data"
          :selectable="$can.delete(`${screen}_attachment`)"
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
import SaveAttachment from '@/orangehrmPimPlugin/components/SaveAttachment';
import EditAttachment from '@/orangehrmPimPlugin/components/EditAttachment';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {parseDate, formatDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  name: 'ProfileAttachments',
  components: {
    'save-attachment': SaveAttachment,
    'edit-attachment': EditAttachment,
    'profile-action-header': ProfileActionHeader,
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    employeeId: {
      type: String,
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
    screen: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.employeeId}/screen/${props.screen}/attachments`,
    );
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    const attachmentDataNormalizer = (data) => {
      return data.map((item) => {
        return {
          ...item,
          attachedDate: formatDate(parseDate(item.attachedDate), jsDateFormat, {
            locale,
          }),
          size: convertFilesizeToString(item.size, 2),
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
          title: this.$t('general.file_name'),
          style: {flex: 1},
        },
        {
          name: 'description',
          title: this.$t('general.description'),
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
      showSaveModal: false,
      showEditModal: false,
      editModalState: null,
    };
  },

  computed: {
    tableHeaders() {
      const headerActions = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {},
      };
      if (this.$can.update(`${this.screen}_attachment`)) {
        headerActions.cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
      }
      if (this.$can.delete(`${this.screen}_attachment`)) {
        headerActions.cellConfig.delete = {
          onClick: this.onClickDelete,
          component: 'oxd-icon-button',
          props: {
            name: 'trash',
          },
        };
      }
      if (this.$can.read(`${this.screen}_attachment`)) {
        headerActions.cellConfig.download = {
          onClick: this.onClickDownload,
          props: {
            name: 'download',
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
    onClickDownload(item) {
      const downUrl = `${window.appGlobal.baseUrl}/pim/viewAttachment/empNumber/${this.employeeId}/attachId/${item.id}`;
      window.open(downUrl, '_blank');
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

<style scoped>
.oxd-divider {
  margin-top: 0;
  margin-bottom: 0;
}
.orangehrm-attachment {
  border-bottom-right-radius: 1.2rem;
  overflow: hidden;
}
</style>
