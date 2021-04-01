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
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6">Job Title List</oxd-text>
        <div>
          <oxd-button label="Add" displayType="secondary" @click="onClickAdd" />
        </div>
      </div>
      <oxd-divider class="orangehrm-horizontal-margin" />
      <div>
        <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
          <div v-if="checkedItems.length > 0">
            <oxd-text tag="span">
              {{ checkedItems.length }} Job Title Selected
            </oxd-text>
            <oxd-button
              label="Delete Selected"
              displayType="label-danger"
              @click="onClickDeleteSelected"
              class="orangehrm-horizontal-margin"
            />
          </div>
          <oxd-text tag="span" v-else> {{ total }} Job Title Found</oxd-text>
        </div>
      </div>
      <div class="orangehrm-container">
        <oxd-card-table
          ref="dTable"
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          v-model:selected="checkedItems"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          :length="pages"
          v-model:current="currentPage"
        />
      </div>
    </div>
  </div>
</template>

<script>
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import {navigate} from '@orangehrm/core/util/helper/navigation';

export default {
  data() {
    return {
      headers: [
        {name: 'title', title: 'Job Title', style: {flex: 2}},
        {name: 'description', title: 'Description', style: {flex: 4}},
        {
          name: 'actions',
          title: 'Actions',
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
      editItem: null,
      checkedItems: [],
    };
  },

  setup() {
    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate('api/v1/admin/job-titles');
    return {
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

  methods: {
    onClickAdd() {
      navigate('/admin/saveJobTitle');
    },
    onClickEdit(item) {
      navigate('/admin/saveJobTitle/{id}', {id: item.id});
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach(index => {
        ids.push(this.items?.data[index].id);
      });
      this.deleteItems(ids);
    },
    onClickDelete(item) {
      const id = item.id;
      this.deleteItems([id]);
    },
    deleteItems(items) {
      // TODO: Loading
      if (items instanceof Array) {
        this.$http
          .delete('api/v1/admin/job-titles', {
            data: {ids: items},
          })
          .then(() => {
            this.resetDataTable();
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    async resetDataTable() {
      this.$refs.dTable.checkedItems = [];
      await this.execQuery();
    },
  },
};
</script>
