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
  <div class="orangehrm-attachment">
    <div class="orangehrm-paper-container">
      <oxd-divider />
      <div class="orangehrm-attachment-header">
        <oxd-text tag="h6">Attachments</oxd-text>
        <oxd-button
          label="Add"
          iconName="plus"
          displayType="text"
          @click="onClickAdd"
        />
      </div>
      <oxd-divider class="orangehrm-horizontal-margin" />
      <div>
        <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
          <div v-if="checkedItems.length > 0">
            <oxd-text tag="span">
              {{ itemsSelectedText }}
            </oxd-text>
            <oxd-button
              label="Delete Selected"
              iconName="trash-fill"
              displayType="label-danger"
              @click="onClickDeleteSelected"
              class="orangehrm-horizontal-margin"
            />
          </div>
          <oxd-text tag="span" v-else>{{ itemsCountText }}</oxd-text>
        </div>
      </div>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          :clickable="false"
          :loading="isLoading"
          v-model:selected="checkedItems"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
      <div v-if="showPaginator" class="orangehrm-bottom-container">
        <oxd-pagination :length="pages" v-model:current="currentPage" />
      </div>
    </div>

    <delete-confirmation ref="deleteDialog"></delete-confirmation>
    <save-attachment v-if="1 == 0" :http="http"></save-attachment>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
// import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import SaveAttachment from '@/orangehrmPimPlugin/components/SaveAttachment';

export default {
  name: 'profile-attachments',
  components: {
    'save-attachment': SaveAttachment,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/job-categories',
    );
    // TODO: Change after API
    // const {
    //   showPaginator,
    //   currentPage,
    //   total,
    //   pages,
    //   pageSize,
    //   response,
    //   isLoading,
    //   execQuery,
    // } = usePaginate(http);
    return {
      http,
      showPaginator: false,
      currentPage: 1,
      isLoading: false,
      total: 1,
      pages: 1,
      pageSize: 1,
      items: {
        data: [
          {
            id: 1,
            name: 'File',
            description: 'short description',
            size: '456Kb',
            type: 'Image',
            date: '2021-05-20',
            addedBy: 'admin',
          },
        ],
        meta: [],
        error: false,
      },
    };
  },
  data() {
    return {
      headers: [
        {name: 'name', slot: 'title', title: 'File Name', style: {flex: 1}},
        {name: 'description', title: 'Description', style: {flex: 1}},
        {name: 'size', title: 'Size', style: {flex: 1}},
        {name: 'type', title: 'Type', style: {flex: 1}},
        {name: 'date', title: 'Date Added', style: {flex: 1}},
        {name: 'addedBy', title: 'Added By', style: {flex: 1}},
        {
          name: 'actions',
          title: 'Actions',
          slot: 'action',
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
    };
  },
  // props: {
  //   tabs: {
  //     type: Array,
  //     required: true,
  //   },
  // },

  computed: {
    itemsCountText() {
      return this.total === 0
        ? 'No Attachments Found'
        : `${this.total} Attachment(s) Found`;
    },
    itemsSelectedText() {
      return `${this.checkedItems.length} Attachment(s) selected`;
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-attachment {
  &-header {
    display: flex;
    padding: 1.2rem;
    button {
      margin-left: 1rem;
    }
  }
}
</style>
