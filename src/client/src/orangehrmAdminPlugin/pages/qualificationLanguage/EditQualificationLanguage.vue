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
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('general.edit_language') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-input-field
            v-model="language.name"
            :label="$t('general.name')"
            :rules="rules.name"
            required
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import useServerValidation from '@/core/util/composable/useServerValidation';

export default {
  props: {
    qualificationLanguageId: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/languages',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const langugageUniqueValidation = createUniqueValidator(
      'Language',
      'name',
      {entityId: props.qualificationLanguageId},
    );

    return {
      http,
      langugageUniqueValidation,
    };
  },

  data() {
    return {
      isLoading: false,
      language: {
        id: '',
        name: '',
      },
      rules: {
        name: [
          required,
          shouldNotExceedCharLength(120),
          this.langugageUniqueValidation,
        ],
      },
    };
  },

  created() {
    this.isLoading = true;
    this.http
      .get(this.qualificationLanguageId)
      .then((response) => {
        const {data} = response.data;
        this.language.id = data.id;
        this.language.name = data.name;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.qualificationLanguageId, {
          name: this.language.name,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/admin/viewLanguages');
    },
  },
};
</script>
