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
      <oxd-form @submitValid="onSubmit" @reset="onReset">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                :value="languagePackage"
                :label="$t('admin.language_package')"
                disabled
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :value="sourceLanguage"
                :label="$t('admin.source_language')"
                disabled
              />
            </oxd-grid-item>
            <br />
            <oxd-grid-item>
              <language-group-list-dropdown v-model="filters.groupId" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('admin.source_text')"
                v-model="filters.sourceText"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('admin.translated_text')"
                v-model="filters.translatedText"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.onlyTranslated"
                type="select"
                :label="$t('admin.show')"
                :options="translationOptions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="filters.sortOrder"
                type="select"
                :label="$t('admin.order')"
                :options="sortOptions"
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
      <oxd-form>
        <div class="orangehrm-header-container">
          <oxd-pagination v-model:current="currentPage" :length="pages" />
        </div>
        <table-header :total="total" :selected="0"></table-header>
        <edit-translations
          v-if="items?.data && !isLoading"
          v-model:langstrings="items.data"
        ></edit-translations>
        <div v-else class="orangehrm-loader">
          <oxd-loading-spinner />
        </div>
        <oxd-form-actions>
          <div class="orangehrm-bottom-container">
            <div>
              <oxd-button
                display-type="secondary"
                :label="$t('general.save')"
                type="reset"
              />
              <oxd-button
                class="orangehrm-left-space"
                display-type="ghost"
                :label="$t('general.cancel')"
                type="submit"
              />
            </div>
          </div>
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import {APIService} from '@/core/util/services/api.service';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import GroupListDropdown from '@/orangehrmAdminPlugin/components/LanguageGroupListDropdown.vue';
import EditTranslationModal from '@/orangehrmAdminPlugin/components/EditTranslationModal.vue';

const defaultFilters = {
  sourceText: null,
  translatedText: null,
  groupId: null,
  sortOrder: null,
  onlyTranslated: null,
};

export default {
  name: 'LanguageTranslationList',
  components: {
    'language-group-list-dropdown': GroupListDropdown,
    'edit-translations': EditTranslationModal,
    'oxd-loading-spinner': Spinner,
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
    const {$t} = usei18n();

    const translationOptions = ref([
      {id: 1, label: $t('admin.all'), value: null},
      {id: 2, label: $t('admin.translated'), value: true},
      {id: 3, label: $t('admin.not_translated'), value: false},
    ]);

    const sortOptions = ref([
      {id: 'ASC', label: $t('admin.ascending')},
      {id: 'DESC', label: $t('admin.descending')},
    ]);

    const filters = ref({...defaultFilters, sortOrder: sortOptions.value[0]});

    const serializedFilters = computed(() => {
      return {
        sourceText: filters.value.sourceText,
        translatedText: filters.value.translatedText,
        groupId: filters.value.groupId?.id,
        sortOrder: filters.value.sortOrder?.id,
        onlyTranslated: filters.value.onlyTranslated?.value,
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
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {query: serializedFilters});

    const onReset = () => {
      filters.value = {...defaultFilters, sortOrder: sortOptions.value[0]};
      execQuery();
    };

    const onSubmit = () => {
      execQuery();
    };

    return {
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      items: response,
      filters,
      translationOptions,
      sortOptions,
      onReset,
      onSubmit,
    };
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-header-container {
  flex-direction: row-reverse;
}
.orangehrm-loader {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80px;
}
</style>
