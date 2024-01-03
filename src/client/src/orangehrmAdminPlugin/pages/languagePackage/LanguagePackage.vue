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
          {{ $t('general.language_packages') }}
        </oxd-text>
        <div>
          <oxd-button
            :label="$t('general.add')"
            icon-name="plus"
            display-type="secondary"
            @click="onClickAddLanguage"
          />
        </div>
      </div>
      <table-header
        :loading="isLoading"
        :total="total"
        :selected="0"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
          :selectable="false"
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
    <add-language-modal
      v-if="showAddLanguageModal"
      @close="onAddLanguageModalClose"
    >
    </add-language-modal>
  </div>
</template>
<script>
import {computed, ref} from 'vue';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@ohrm/core/util/composable/useSort';
import AddLanguageModal from '@/orangehrmAdminPlugin/components/AddLanguageModal.vue';
import {urlFor} from '@/core/util/helper/url';

const defaultFilters = {
  languageName: '',
};

const defaultSortOrder = {
  languageName: 'ASC',
};

export default {
  name: 'LanguagePackageList',

  components: {
    'add-language-modal': AddLanguageModal,
  },

  setup() {
    const filters = ref({...defaultFilters});

    const {sortDefinition, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        sortOrder: sortOrder.value,
        activeOnly: true,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/i18n/languages',
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
    } = usePaginate(http, {
      query: serializedFilters,
    });

    onSort(execQuery);

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
      filters,
      sortDefinition,
    };
  },
  data() {
    return {
      showAddLanguageModal: false,
      headers: [
        {
          name: 'name',
          slot: 'title',
          title: this.$t('general.language_packages'),
          sortField: 'languageName',
          style: {flex: '70%'},
        },
        {
          name: 'actions',
          slot: 'footer',
          title: this.$t('general.actions'),
          cellType: 'oxd-table-cell-actions',
          style: {flex: '30%'},
          cellConfig: {
            translate: {
              component: 'oxd-button',
              onClick: this.onClickTranslate,
              props: {
                label: this.$t('admin.translate'),
                style: 'Text',
                displayType: 'text',
              },
            },
            export: {
              component: 'oxd-button',
              props: {
                label: this.$t('admin.export'),
                displayType: 'text',
                size: 'medium',
              },
              onClick: this.onClickExport,
            },
          },
        },
      ],
    };
  },
  methods: {
    onClickAddLanguage() {
      this.showAddLanguageModal = true;
    },
    onAddLanguageModalClose() {
      this.showAddLanguageModal = false;
      this.reloadLanguages();
    },
    onCancel() {
      navigate('/admin/languagePackage');
    },
    async reloadLanguages() {
      await this.execQuery();
    },
    onClickTranslate(item) {
      navigate('/admin/languageCustomization/{id}', {id: item.id});
    },
    onClickExport(item) {
      const url = urlFor('/admin/viewLanguagePackage/languageId/{languageId}', {
        languageId: item.id,
      });
      window.open(url, '_blank');
    },
  },
};
</script>
<style src="./language-package.scss" lang="scss" scoped></style>
