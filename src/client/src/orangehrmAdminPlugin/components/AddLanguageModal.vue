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
  <oxd-dialog @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('admin.add_language_package') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <languages-dropdown
          v-model="language"
          :rules="rules.language"
        ></languages-dropdown>
      </oxd-form-row>
      <oxd-form-row>
        <div class="addlanguage-note">
          <div class="addlanguage-note-text">
            <oxd-text class="orangehrm-sub-title">
              {{ $t('general.note') }}
            </oxd-text>
            <oxd-text class="orangehrm-information-card-text">
              {{ $t('admin.translate_text_manually') }}
            </oxd-text>
          </div>
        </div>
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions class="orangehrm-form-action">
        <required-text />
        <oxd-button
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <oxd-button
          display-type="secondary"
          :label="$t('general.save')"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import LanguagesDropdown from '@/orangehrmAdminPlugin/components/LanguagesDropdown.vue';
import {required} from '@/core/util/validation/rules';
import {OxdDialog} from '@ohrm/oxd';

export default {
  name: 'AddLanguageModal',
  components: {
    'oxd-dialog': OxdDialog,
    'languages-dropdown': LanguagesDropdown,
  },
  emits: ['close'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/i18n/languages',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      language: null,
      rules: {
        language: [required],
      },
    };
  },
  methods: {
    onSave() {
      this.http
        .update(this.language.id, null)
        .then((response) => {
          if (response) {
            return this.$toast.saveSuccess();
          }
        })
        .then(() => {
          this.isLoading = false;
          this.$emit('close');
        });
    },
    onCancel() {
      this.$emit('close');
    },
  },
};
</script>
<style
  src="../pages/languagePackage/language-package.scss"
  lang="scss"
  scoped
></style>
