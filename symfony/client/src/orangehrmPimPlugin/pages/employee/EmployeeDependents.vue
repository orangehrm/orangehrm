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
  <edit-employee-layout :employee-id="empNumber">
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
    <div class="orangehrm-horizontal-padding orangehrm-top-padding">
      <profile-action-header @click="onClickAdd">
        Assigned Dependents
      </profile-action-header>
      <oxd-divider />
    </div>
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
    <div class="orangehrm-bottom-container">
      <oxd-pagination
        v-if="showPaginator"
        :length="pages"
        v-model:current="currentPage"
      />
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </edit-employee-layout>
</template>

<script>
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import {APIService} from '@orangehrm/core/util/services/api.service';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import SaveDependent from '@/orangehrmPimPlugin/components/SaveDependent';
import EditDependent from '@/orangehrmPimPlugin/components/EditDependent';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';

const dependentNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      dateOfBirth: item.dateOfBirth,
      name: item.name,
      relationship:
        item.relationshipType == 'other' ? item.relationship : 'Child',
    };
  });
};

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
      `api/v2/pim/employees/${props.empNumber}/dependents`,
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
    } = usePaginate(http, {}, dependentNormalizer);
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
        {name: 'name', slot: 'title', title: 'Name', style: {flex: 1}},
        {name: 'relationship', title: 'Relationship', style: {flex: 1}},
        {name: 'dateOfBirth', title: 'Date of Birth', style: {flex: 1}},
        {
          name: 'actions',
          slot: 'action',
          title: 'Actions',
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
            return this.$toast.success({
              title: 'Success',
              message: 'Successfully Deleted',
            });
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

  computed: {
    itemsCountText() {
      return this.total === 0
        ? 'No Records Found'
        : `${this.total} Dependent(s) Found`;
    },
    itemsSelectedText() {
      return `${this.checkedItems.length} Dependent(s) Selected`;
    },
  },
};
</script>
