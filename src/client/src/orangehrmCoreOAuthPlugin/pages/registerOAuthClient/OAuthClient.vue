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
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.oauth_client_list') }}
        </oxd-text>
        <div>
          <oxd-button
            :label="$t('general.add')"
            icon-name="plus"
            display-type="secondary"
            @click="onClickAdd"
          />
        </div>
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
    </div>

    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import usei18n from '@/core/util/composable/usei18n';

export default {
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    unselectableClientIds: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const {$t} = usei18n();

    const oAuthClientNormalizer = (data) => {
      return data.map((item) => {
        const selectable = props.unselectableClientIds.findIndex(
          (id) => id === item.clientId,
        );
        return {
          id: item.id,
          name: item.name,
          redirectUri: item.redirectUri,
          enabled: item.enabled
            ? $t('general.enabled')
            : $t('general.disabled'),
          isSelectable: selectable === -1,
          isDisabled: selectable !== -1,
        };
      });
    };

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/oauth-clients',
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
    } = usePaginate(http, {normalizer: oAuthClientNormalizer});
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
          title: this.$t('general.name'),
          style: {flex: 2},
        },
        {
          name: 'redirectUri',
          title: this.$t('admin.redirect_uri'),
          style: {flex: 3},
        },
        {
          name: 'enabled',
          title: this.$t('general.status'),
          style: {flex: 2},
        },
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

  methods: {
    onClickAdd() {
      navigate('/admin/saveOAuthClient');
    },
    onClickEdit(item) {
      navigate('/admin/editOAuthClient', {}, {id: item.id});
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
