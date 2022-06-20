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
  <oxd-dialog
    :style="{width: '90%', maxWidth: '850px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('general.add_language') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <languages-dropdown v-model="language.languageId"></languages-dropdown>
      </oxd-form-row>
      <oxd-form-row>
        <!-- change to text -->
        <div class="addlanguage-note">
          <oxd-text type="card-title">Note: </oxd-text>
          <br />
          <oxd-text type="subtitle-1">
            Users will require translate texts manually after creating the
            language package.
          </oxd-text>
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
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import LanguagesDropdown from '@/orangehrmAdminPlugin/components/LanguagesDropdown.vue';

const languageModel = {
  languageId: '',
};

export default {
  name: 'AddLanguageModal',
  components: {
    'oxd-dialog': Dialog,
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
      language: {...languageModel},
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: `api/v2/admin/i18n/languages/${this.language.languageId.id}`,
        })
        .then(response => {
          if (response) {
            return this.$toast.updateSuccess();
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
