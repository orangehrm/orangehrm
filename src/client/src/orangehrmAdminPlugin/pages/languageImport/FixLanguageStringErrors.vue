<!--
  - OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  - all the essential functionalities required for any enterprise.
  - Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
  -
  - OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
  - the GNU General Public License as published by the Free Software Foundation, either
  - version 3 of the License, or (at your option) any later version.
  -
  - OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  - without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  - See the GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License along with OrangeHRM.
  - If not, see <https://www.gnu.org/licenses/>.
  -->

<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <oxd-form
        v-if="totalItems > 0"
        :loading="isLoading"
        @submit-valid="onSubmitLangString"
        @reset="onReset"
      >
        <div class="orangehrm-header-container">
          <oxd-text tag="h6" class="orangehrm-main-title">
            {{ $t('admin.errors_in_import_language_packages') }}
          </oxd-text>
          <oxd-pagination
            v-if="showPaginator"
            :key="currentPage"
            v-model:current="currentPage"
            :length="totalPages"
          />
        </div>
        <table-header
          :loading="isLoading"
          :total="xliffSourceAndTargetValidationErrors.length"
          :selected="0"
        ></table-header>
        <edit-translations
          v-if="paginatedErrors.length"
          v-model:langstrings="paginatedErrors"
          :xliff-source-and-target-validation-errors="paginatedErrors"
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
import {ref, computed, watch} from 'vue';
import useToast from '@/core/util/composable/useToast';
import {reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import FixLanguageStringErrorTable from '@/orangehrmAdminPlugin/components/FixLanguageStringErrorTable.vue';

export default {
  name: 'FixLanguageStringErrors',
  components: {
    'edit-translations': FixLanguageStringErrorTable,
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
    xliffSourceAndTargetValidationErrors: {
      type: Array,
      required: true,
    },
  },

  setup(props) {
    const {saveSuccess} = useToast();
    const itemsPerPage = 10;

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/admin/i18n/languages/${props.languageId}/translations`,
    );

    const currentPage = ref(1);
    const isLoading = ref(false);

    const totalItems = computed(
      () => props.xliffSourceAndTargetValidationErrors.length,
    );
    const totalPages = computed(() =>
      Math.ceil(totalItems.value / itemsPerPage),
    );
    const paginatedErrors = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage;
      return props.xliffSourceAndTargetValidationErrors.slice(
        start,
        start + itemsPerPage,
      );
    });

    const showPaginator = computed(() => totalPages.value > 1);

    watch(totalPages, (newTotalPages) => {
      if (currentPage.value > newTotalPages) {
        currentPage.value = newTotalPages;
      }
    });

    const onSubmitLangString = () => {
      isLoading.value = true;
      http
        .request({
          method: `PUT`,
          url: `/api/v2/admin/i18n/languages/${props.languageId}/translations/bulk`,
          data: {
            data: paginatedErrors.value.data
              .filter((item) => item.target !== null && item.modified == true)
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
        .then(() => reloadPage());
    };

    return {
      showPaginator,
      currentPage,
      isLoading,
      totalItems,
      totalPages,
      paginatedErrors,
      onSubmitLangString,
    };
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
