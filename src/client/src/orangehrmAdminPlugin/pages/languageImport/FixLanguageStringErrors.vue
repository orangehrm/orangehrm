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
    <oxd-alert
      :show="!isLoading && showPaginator && itemsModified"
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
          <oxd-text tag="h6" class="orangehrm-main-title">
            {{ $t('admin.errors_in_import_language_packages') }}
          </oxd-text>
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
          records-found-lang-string="admin.n_errors_found"
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
                :label="$t('general.reset')"
                type="reset"
              />
              <oxd-button
                class="orangehrm-left-space"
                display-type="secondary"
                :label="$t('general.save')"
                type="submit"
                :disabled="isLoading"
              />
              <span v-if="isLoading" class="loading-spinner"></span>
            </div>
          </div>
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import FixLanguageStringErrorTable from '@/orangehrmAdminPlugin/components/FixLanguageStringErrorTable.vue';
import usePaginate from '@/core/util/composable/usePaginate';
import {navigate} from '@/core/util/helper/navigation';
import {ref} from 'vue';
import {OxdAlert} from '@ohrm/oxd';

export default {
  name: 'FixLanguageStringErrors',
  components: {
    'oxd-alert': OxdAlert,
    'edit-translations': FixLanguageStringErrorTable,
  },
  props: {
    languageId: {
      type: Number,
      required: true,
    },
    empNumber: {
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
    const {saveSuccess} = useToast();

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/admin/i18n/languages/${props.languageId}/translations/errors`,
    );

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      response: items,
      isLoading,
      execQuery,
    } = usePaginate(http);

    const itemsModified = ref(false);

    const onReset = () => {
      currentPage.value = 1;
      itemsModified.value = false;
      execQuery();
    };

    const checkItemsModified = () => {
      itemsModified.value = items.value.data.reduce(
        (accumulator, item) =>
          accumulator || (item.target !== '' && item.modified === true),
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
          currentPage.value = 1;
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
      onReset,
      onSubmitLangString,
      itemsModified,
      checkItemsModified,
    };
  },

  watch: {
    total(value) {
      if (value === 0) {
        navigate('/admin/languagePackage');
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-header-container {
  display: flex;
  align-items: center;
}

.orangehrm-main-title {
  text-align: left;
}

.pagination {
  margin-left: auto;
}
</style>
