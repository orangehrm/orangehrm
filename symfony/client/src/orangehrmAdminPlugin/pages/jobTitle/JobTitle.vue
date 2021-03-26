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
import usePaginate from '@/core/util/composable/usePaginate';

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
      // TODO: Add url
      console.log('go to add screen');
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach(index => {
        ids.push(this.items?.data[index].id);
      });
      this.callDelete(ids);
    },
    onClickDelete(item) {
      const id = item.id;
      this.callDelete([id]);
    },
    callDelete(ids) {
      const headers = new Headers();
      headers.append('Content-Type', 'application/json');
      headers.append('Accept', 'application/json');

      fetch(`${this.global.baseUrl}/api/v1/admin/job-titles`, {
        method: 'DELETE',
        headers: headers,
        body: JSON.stringify({
          ids: ids,
        }),
      }).then(async res => {
        if (res.status === 200) {
          window.location.reload();
          // this.currentPage = 1;
          // this.checkedItems = [];
          // this.fetchData();
        } else {
          console.error(res);
        }
      });
    },
    onClickEdit(item) {
      //TODO: Add path
      console.log(item);
    },
  },
};
</script>
