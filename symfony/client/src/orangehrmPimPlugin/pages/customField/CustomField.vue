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
        <div class="orangehrm-custom-field-title">
          <oxd-text tag="h6" class="orangehrm-main-title">
            Custom Fields
          </oxd-text>
          <oxd-text class="--infotext" tag="p" v-if="remainingFields > 0">
            Remaining Number of Custom Fields: {{ remainingFields }}
          </oxd-text>
          <oxd-text class="--infotext" tag="p" v-else>
            All Customs Fields are in use
          </oxd-text>
        </div>
        <oxd-button
          label="Add"
          iconName="plus"
          displayType="secondary"
          @click="onClickAdd"
          v-if="remainingFields > 0"
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
          :loading="isLoading"
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          :clickable="false"
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

    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import usePaginate from '@orangehrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@orangehrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@orangehrm/core/util/helper/navigation';
import {APIService} from '@orangehrm/core/util/services/api.service';

export default {
  props: {
    customFieldLimit: {
      type: Number,
      required: true,
    },
    screenList: {
      type: Array,
      required: true,
    },
    fieldTypeList: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      screenWidth: screen.width,
      headers: [
        {
          name: 'fieldName',
          slot: 'title',
          title: 'Custom Field Name',
          style: {flex: 2},
        },
        {name: 'screen', title: 'Screen', style: {flex: 2}},
        {name: 'fieldType', title: 'Field Type', style: {flex: 2}},
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

  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/custom-fields',
    );
    const dataNormalizer = data => {
      return data.map(item => {
        return {
          id: item.id,
          fieldName: item.fieldName,
          screen: props.screenList.filter(screen => {
            return item.screen === screen.id;
          })[0].label,
          fieldType: props.fieldTypeList.filter(fieldType => {
            return item.fieldType === fieldType.id;
          })[0].label,
          extraData: item.extraData,
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
    } = usePaginate(http, {}, dataNormalizer);

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total: total,
      pages,
      pageSize,
      execQuery,
      items: response,
    };
  },
  methods: {
    onClickAdd() {
      navigate('/pim/saveCustomFields');
    },
    onClickEdit(item) {
      navigate('/pim/saveCustomFields/{id}', {id: item.id});
    },
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
  },

  computed: {
    remainingFields() {
      return this.customFieldLimit - this.items?.data?.length;
    },
  },
};
</script>

<style src="./customField.scss" lang="scss" scoped></style>
