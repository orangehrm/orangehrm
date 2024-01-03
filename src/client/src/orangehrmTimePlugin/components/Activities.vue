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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('time.activities') }}
      </oxd-text>
      <div class="orangehrm-main-actions">
        <oxd-button
          :label="$t('time.copy_from')"
          display-type="ghost"
          @click="onClickCopyFrom"
        />
        <oxd-button
          :label="$t('general.add')"
          icon-name="plus"
          display-type="secondary"
          @click="onClickAdd"
        />
      </div>
    </div>
    <table-header
      :total="total"
      :loading="isLoading"
      :selected="checkedItems.length"
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
    <copy-activity-modal
      v-if="showCopyFromModal"
      :project-id="projectId"
      @close="onCloseModal"
    ></copy-activity-modal>
    <save-activity-modal
      v-if="showSaveActivityModal"
      :project-id="projectId"
      @close="onCloseModal"
    ></save-activity-modal>
    <edit-activity-modal
      v-if="showEditActivityModal"
      :project-id="projectId"
      :activity-id="editActivityModalState"
      @close="onCloseModal"
    ></edit-activity-modal>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import CopyActivityModal from '@/orangehrmTimePlugin/components/CopyActivityModal.vue';
import SaveActivityModal from '@/orangehrmTimePlugin/components/SaveActivityModal.vue';
import EditActivityModal from '@/orangehrmTimePlugin/components/EditActivityModal.vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';

export default {
  name: 'Activities',

  components: {
    'copy-activity-modal': CopyActivityModal,
    'save-activity-modal': SaveActivityModal,
    'edit-activity-modal': EditActivityModal,
    'delete-confirmation': DeleteConfirmationDialog,
  },

  props: {
    projectId: {
      type: Number,
      required: true,
    },
    unselectableIds: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/time/project/${props.projectId}/activities`,
    );

    const activitiesNormalizer = (data) => {
      return data.map((item) => {
        const selectable = props.unselectableIds.findIndex(
          (id) => id == item.id,
        );
        return {
          ...item,
          isSelectable: selectable === -1,
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
      normalizer: activitiesNormalizer,
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
          title: this.$t('time.activity_name'),
          style: {'flex-basis': '80%'},
        },
        {
          name: 'actions',
          title: this.$t('general.actions'),
          slot: 'action',
          style: {'flex-shrink': 1},
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
      showCopyFromModal: false,
      showSaveActivityModal: false,
      showEditActivityModal: false,
      editActivityModalState: null,
    };
  },

  methods: {
    onClickAdd() {
      this.showSaveActivityModal = true;
    },
    onClickEdit(item) {
      this.editActivityModalState = item.id;
      this.showEditActivityModal = true;
    },
    onClickCopyFrom() {
      this.showCopyFromModal = true;
    },
    onCloseModal() {
      this.showCopyFromModal = false;
      this.showSaveActivityModal = false;
      this.showEditActivityModal = false;
      this.resetDataTable();
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
    onClickDelete(item) {
      const isSelectable = this.unselectableIds.findIndex(
        (id) => id == item.id,
      );
      if (isSelectable > -1) {
        return this.$toast.error({
          title: this.$t('general.error'),
          message: this.$t(
            'time.not_allowed_to_delete_project_activities_which_have_time_logged_against',
          ),
        });
      }
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
            this.$toast.deleteSuccess();
            this.isLoading = false;
            this.resetDataTable();
          });
      }
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-main-actions {
  gap: 0.4rem;
  display: flex;
  flex-direction: column;
  ::v-deep(.oxd-button--medium) {
    width: 100%;
  }
  @include oxd-respond-to('md') {
    flex-direction: row;
    ::v-deep(.oxd-button--medium) {
      width: unset;
    }
  }
}
</style>
