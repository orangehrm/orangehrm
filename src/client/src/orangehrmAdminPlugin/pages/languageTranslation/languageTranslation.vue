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
    <oxd-table-filter :filter-title="$t('admin.translate_language_package')">
      <oxd-form @submit-valid="onSubmit" @reset="onReset">
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
          </oxd-grid>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <language-group-list-dropdown v-model="filters.groupId" />
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
    <oxd-alert
      :show="!isLoading && itemsModified"
      type="info"
      :message="$t('admin.please_save_before_pagination')"
    ></oxd-alert>
    <div class="orangehrm-paper-container">
      <oxd-form
        v-if="total > 0"
        :loading="isLoading"
        @submit-valid="onSubmitLangString"
        @reset="onReset"
      >
        <div class="orangehrm-header-container">
          <oxd-pagination
            v-if="showPaginator && !itemsModified"
            :key="currentPage"
            v-model:current="currentPage"
            :length="pages"
          />
        </div>
        <table-header
          :loading="isLoading"
          :total="total"
          :selected="0"
        ></table-header>
        <edit-translations
          v-if="items?.data"
          v-model:langstrings="items.data"
          @update:langstrings="checkItemsModified"
        ></edit-translations>
        <oxd-form-actions>
          <div class="orangehrm-bottom-container">
            <div>
              <oxd-button
                display-type="ghost"
                :label="$t('general.cancel')"
                type="reset"
              />
              <oxd-button
                class="orangehrm-left-space"
                display-type="secondary"
                :label="$t('general.save')"
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
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import EditTranslationTable from '@/orangehrmAdminPlugin/components/EditTranslationTable.vue';
import GroupListDropdown from '@/orangehrmAdminPlugin/components/LanguageGroupListDropdown.vue';
import {OxdAlert} from '@ohrm/oxd';

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
    'edit-translations': EditTranslationTable,
    'oxd-alert': OxdAlert,
  },
  props: {
    languageId: {
      type: Number,
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

    const {saveSuccess} = useToast();

    const translationOptions = ref([
      {id: 1, label: $t('admin.all'), value: null},
      {id: 2, label: $t('admin.translated'), value: true},
      {id: 3, label: $t('admin.not_translated'), value: false},
    ]);

    const sortOptions = ref([
      {id: 'ASC', label: $t('general.ascending')},
      {id: 'DESC', label: $t('general.descending')},
    ]);

    const filters = ref({
      ...defaultFilters,
      sortOrder: sortOptions.value[0],
      onlyTranslated: translationOptions.value[0],
    });

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
      response: items,
      isLoading,
      execQuery,
    } = usePaginate(http, {query: serializedFilters});

    const itemsModified = ref(false);

    const onReset = () => {
      currentPage.value = 1;
      itemsModified.value = false;
      filters.value = {...defaultFilters, sortOrder: sortOptions.value[0]};
      execQuery();
    };

    const onSubmit = () => {
      itemsModified.value = false;
      execQuery();
    };

    const checkItemsModified = () => {
      itemsModified.value = items.value.data.reduce(
        (accumulator, item) =>
          accumulator ||
          (item.target !== null &&
            item.oldTarget !== item.target &&
            item.modified === true),
        false,
      );
    };

    const onSubmitLangString = () => {
      isLoading.value = true;
      http
        .request({
          method: `PUT`,
          url: `/api/v2/admin/i18n/languages/${props.languageId}/translations/bulk`,
          data: {
            data: items.value.data
              .filter((item) => item.target !== null && item.modified === true)
              .map((item) => {
                return {
                  langStringId: item.langStringId,
                  translatedValue: item.target,
                };
              }),
          },
        })
        .then(() => {
          return saveSuccess();
        })
        .then(() => {
          itemsModified.value = false;
          execQuery();
        });
    };

    return {
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      items,
      filters,
      translationOptions,
      sortOptions,
      onReset,
      onSubmit,
      onSubmitLangString,
      itemsModified,
      checkItemsModified,
    };
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-header-container {
  justify-content: end;
}
</style>
