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
  <div class="orangehrm-horizontal-padding orangehrm-top-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">{{
      $t('general.add_language')
    }}</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              :key="allowedLanguages"
              v-model="language.languageId"
              type="select"
              :label="$t('general.language')"
              :options="allowedLanguages"
              :rules="rules.languageId"
              :clear="false"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              :key="allowedFluencies"
              v-model="language.fluencyId"
              type="select"
              :label="$t('pim.fluency')"
              :options="allowedFluencies"
              :rules="rules.fluencyId"
              :clear="false"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="language.competencyId"
              type="select"
              :label="$t('pim.competency')"
              :options="competencies"
              :rules="rules.competencyId"
              :clear="false"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="--span-column-2">
            <oxd-input-field
              v-model="language.comment"
              type="textarea"
              :label="$t('general.comments')"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

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
    <oxd-divider />
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

const languageModel = {
  languageId: null,
  fluencyId: null,
  competencyId: null,
  comment: '',
};

export default {
  name: 'SaveLanguage',

  props: {
    http: {
      type: Object,
      required: true,
    },
    fluencies: {
      type: Array,
      required: true,
    },
    competencies: {
      type: Array,
      required: true,
    },
    api: {
      type: String,
      required: true,
    },
  },

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      language: {...languageModel},
      languages: [],
      rules: {
        languageId: [required],
        fluencyId: [required],
        competencyId: [required],
        comment: [shouldNotExceedCharLength(100)],
      },
    };
  },

  computed: {
    allowedLanguages() {
      return this.languages;
    },
    allowedFluencies() {
      const languageIndex = this.languages.findIndex(
        (item) => item.id === this.language.languageId?.id,
      );
      if (languageIndex > -1) {
        const selectedLanguage = this.languages[languageIndex];
        return this.fluencies.filter((item) => {
          return selectedLanguage.allowedFluencyIds.includes(item.id);
        });
      }
      return [];
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url: this.api,
        params: {limit: 0},
      })
      .then((response) => {
        const {data} = response.data;
        if (Array.isArray(data)) {
          this.languages = data.map((item) => {
            return {
              id: item.id,
              label: item.name,
              allowedFluencyIds: item.allowedFluencyIds
                ? item.allowedFluencyIds
                : [],
            };
          });
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          languageId: this.language.languageId?.id,
          fluencyId: this.language.fluencyId?.id,
          competencyId: this.language.competencyId?.id,
          comment: this.language.comment,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
