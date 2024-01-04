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
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <div class="orangehrm-custom-field-title">
          <oxd-text tag="h6" class="orangehrm-main-title">
            {{ $t('pim.custom_fields') }}
          </oxd-text>
          <template v-if="!isLoading">
            <oxd-text v-if="remainingFields > 0" class="--infotext" tag="p">
              {{ $t('pim.remaining_no_of_custom_fields') }}
              {{ remainingFields }}
            </oxd-text>
            <oxd-text v-else class="--infotext" tag="p">
              {{ $t('pim.all_custom_fields_in_use') }}
            </oxd-text>
          </template>
        </div>
        <oxd-button
          v-if="remainingFields > 0"
          :label="$t('general.add')"
          icon-name="plus"
          display-type="secondary"
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
          :loading="isLoading"
          :headers="headers"
          :items="items?.data"
          :selectable="true"
          :clickable="false"
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
    </div>

    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },
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
    unselectableIds: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/custom-fields',
    );
    const dataNormalizer = (data) => {
      return data.map((item) => {
        const selectable = props.unselectableIds.findIndex(
          (id) => id == item.id,
        );
        return {
          id: item.id,
          fieldName: item.fieldName,
          screen: props.screenList.filter((screen) => {
            return item.screen === screen.id;
          })[0].label,
          fieldType: props.fieldTypeList.filter((fieldType) => {
            return item.fieldType === fieldType.id;
          })[0].label,
          extraData: item.extraData,
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
    } = usePaginate(http, {normalizer: dataNormalizer});

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

  data() {
    return {
      screenWidth: screen.width,
      headers: [
        {
          name: 'fieldName',
          slot: 'title',
          title: this.$t('pim.custom_field_name'),
          style: {flex: 2},
        },
        {name: 'screen', title: this.$t('pim.screen'), style: {flex: 2}},
        {name: 'fieldType', title: this.$t('pim.field_type'), style: {flex: 2}},
        {
          name: 'actions',
          title: this.$t('general.actions'),
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

  computed: {
    isLoaded() {
      return !this.isLoading;
    },
    remainingFields() {
      return this.customFieldLimit - this.items?.data?.length;
    },
  },
  methods: {
    onClickAdd() {
      navigate('/pim/saveCustomFields');
    },
    onClickEdit(item) {
      navigate('/pim/saveCustomFields/{id}', {id: item.id});
    },
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
      const isSelectable = this.unselectableIds.findIndex(
        (id) => id == item.id,
      );
      if (isSelectable > -1) {
        return this.$toast.error({
          title: this.$t('general.error'),
          message: this.$t('pim.custom_fields_in_use'),
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
};
</script>

<style src="./customField.scss" lang="scss" scoped></style>
