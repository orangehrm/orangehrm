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
    <oxd-table-filter :filter-title="$t('admin.translate_language_package')">
      <oxd-form @submitValid="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="languageTranslation.languagePackage"
                :label="$t('admin.language_package')"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="languageTranslation.sourceLanguage"
                :label="$t('admin.source_language')"
                :disabled="!editable"
              />
            </oxd-grid-item>
            <br />
            <oxd-grid-item>
              <module-list-dropdown/>
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.sourceText"
                :label="$t('admin.source_text')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.translatedText"
                :label="$t('admin.translated_text')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('general.show')"
                :options="showCategory"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            :label="$t('general.reset')"
            type="reset"
          />
          <oxd-button
            class="orangehrm-left-space"
            display-type="secondary"
            :label="$t('general.search')"
            type="submit"
          />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>
    <br />
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <div>
          <oxd-button
            display-type="secondary"
            label="Save All"
          ></oxd-button>
          <oxd-button
            display-type="ghost"
            label="Cancel"
          ></oxd-button>
        </div>
      </div>
      <table-header
        :loading="isLoading"
        :total="total"
        :selected="0"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          ref="cardTable"
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
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import useSort from '@ohrm/core/util/composable/useSort';
import {APIService} from '@/core/util/services/api.service';
import Button from '@ohrm/oxd/core/components/Button/Button';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import CardTable from '@ohrm/oxd/core/components/CardTable/CardTable';
import ModuleListDropdown from '@/orangehrmAdminPlugin/components/ModuleListDropdown.vue';

const translationNormalizer = data => {
  return data.map(item => {
    return {
      id: item.id,
      sourceText: item.sourceText,
      translatedText: item.translatedText,
    };
  });
};

const defaultFilters = {
  sourceText: null,
  translatedText: null,
};

const defaultSortOrder = {
  sourceText: 'ASC',
};

export default {
  name: 'LanguageTranslationList',
  components: {
    'module-list-dropdown': ModuleListDropdown,
  },
  props: {
    languageId: {
      type: String,
      required: true,
    },
    languagePackage: {
      type: String,
      required: true,
    },
    sourceLanguage: {
      type: String,
      required: true,
    },
  },

  setup(props) {
    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        sourceText: filters.value.sourceText,
        translatedText: filters.value.translatedText,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/admin/i18n/languages/${props.languageId}/translations`,
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
      normalizer: translationNormalizer,
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
      category: null,
      languageTranslation: {
        languagePackage: this.languagePackage,
        sourceLanguage: this.sourceLanguage,
      },
      headers: [
        {
          name: 'source',
          slot: 'title',
          title: this.$t('admin.source_text'),
          sortField: 'sourceText',
          style: {flex: 3},
        },
        {
          name: 'note',
          title: this.$t('admin.source_note'),
          style: {flex: 2},
        },
        {
          name: 'target',
          title: this.$t('admin.translated_text'),
          style: {flex: 2},
        },
        {
          name: 'actions',
          slot: 'footer',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            edit: {
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ],
      showCategory: [
        {id: 1, label: this.$t('admin.all')},
        {id: 2, label: this.$t('admin.translated')},
        {id: 3, label: this.$t('admin.not_translated')},
      ],
    };
  },
  method: {
    async filterItems() {
      await this.execQuery();
    },
    onClickReset() {
      this.filters = {...defaultFilters};
      this.filterItems();
    },
  }
};
</script>
<style lang=""></style>
