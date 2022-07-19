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
                v-model="languagePackage"
                :label="$t('admin.language_package')"
                disabled
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="sourceLanguage"
                :label="$t('admin.source_language')"
                disabled
              />
            </oxd-grid-item>
            <br />
            <oxd-grid-item>
              <!-- change name -->
              <group-list-dropdown />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field :label="$t('admin.source_text')" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field :label="$t('admin.translated_text')" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('admin.show')"
                :options="showCategory"
              />
            </oxd-grid-item>
            <!-- <oxd-grid-item>
              <oxd-input-field
                type="select"
                :label="$t('general.order')"
                :options="showOrder"
              />
            </oxd-grid-item> -->
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
        <oxd-form-actions>
          <div class="orangehrm-header-container">
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
        <table-header :total="total" :selected="0"></table-header>
        <edit-translations
          v-if="items?.data"
          v-model:langstrings="items.data"
        ></edit-translations>
        <div v-else class="orangehrm-loader">
          <oxd-loading-spinner />
        </div>
        <div class="orangehrm-bottom-container">
          <oxd-pagination v-model:current="currentPage" :length="pages" />
        </div>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';
import useSort from '@ohrm/core/util/composable/useSort';
import useForm from '@ohrm/core/util/composable/useForm';
import Input from '@ohrm/oxd/core/components/Input/Input';
import {APIService} from '@/core/util/services/api.service';
import Button from '@ohrm/oxd/core/components/Button/Button';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import CardTable from '@ohrm/oxd/core/components/CardTable/CardTable';
import GroupListDropdown from '@/orangehrmAdminPlugin/components/GroupListDropdown.vue';
import EditTranslationModal from '@/orangehrmAdminPlugin/components/EditTranslationModal.vue';
import useLanguageTranslations from '@/orangehrmAdminPlugin/util/composable/useLanguageTranslations';

// const translationNormalizer = data => {
//   return data.map(item => {
//     return {
//       id: item.id,
//       source: item.source,
//       target: item.target,
//     };
//   });
// };
const defaultFilters = {
  sourceText: null,
  translatedText: null,
};

const defaultSortOrder = {
  'langString.value': 'DEFAULT',
};

export default {
  name: 'LanguageTranslationList',
  components: {
    'group-list-dropdown': GroupListDropdown,
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
    const filters = ref({...defaultFilters});

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const {formRef, invalid, validate} = useForm();

    // const serializedFilters = computed(() => {
    //   return {
    //     sourceText: filters.value.sourceText,
    //     translatedText: filters.value.translatedText,
    //     sortField: sortField.value,
    //     sortOrder: sortOrder.value,
    //   };
    // });

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/admin/i18n/languages/${props.languageId}/translations`,
    );
    // const http = new APIService(window.appGlobal.baseUrl, '');

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http);

    const {getAllTranslations} = useLanguageTranslations(http);

    // onSort(execQuery);
    return {
      http,
      invalid,
      formRef,
      validate,
      getAllTranslations,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
      // filters,
      // sortDefinition,
    };
  },
  data() {
    return {
      // langstrings: [],
      category: null,
      languageTranslation: {
        languagePackage: this.languagePackage,
        sourceLanguage: this.sourceLanguage,
      },
      showCategory: [
        {id: 1, label: this.$t('admin.all')},
        {id: 2, label: this.$t('admin.translated')},
        {id: 3, label: this.$t('admin.not_translated')},
      ],
      showOrder: [
        {id: 1, label: this.$t('admin.ascending')},
        {id: 2, label: this.$t('admin.decending')},
      ],
    };
  },
  // beforeMount() {
  //   this.isLoading = true;
  //   this.getAllTranslations(this.languageId)
  //     .then(response => {
  //       const {data} = response.data;
  //       this.langstrings = [...data];
  //     })
  //     .finally(() => {
  //       this.isLoading = false;
  //     });
  // },
  method: {
    async filterItems() {
      await this.execQuery();
    },
    onClickReset() {
      this.filters = {...defaultFilters};
      this.filterItems();
    },
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-header-container {
  flex: auto;
}
.orangehrm-loader {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80px;
}
</style>
