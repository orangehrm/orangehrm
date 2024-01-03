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
      $t('general.edit_language')
    }}</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="language.name"
              :label="$t('general.language')"
              required
              readonly
              disabled
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="language.fluency"
              :label="$t('pim.fluency')"
              required
              readonly
              disabled
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
  name: '',
  fluency: '',
  competencyId: null,
  comment: '',
};

export default {
  name: 'EditLanguage',

  props: {
    http: {
      type: Object,
      required: true,
    },
    employeeId: {
      type: String,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
    competencies: {
      type: Array,
      required: true,
    },
  },

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      language: {...languageModel},
      rules: {
        competencyId: [required],
        comment: [shouldNotExceedCharLength(100)],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url: `/api/v2/pim/employees/${this.employeeId}/languages/${this.data.languageId}/fluencies/${this.data.fluencyId}`,
      })
      .then((response) => {
        const {data} = response.data;
        this.language.name = data.language.name;
        this.language.fluency = data.fluency.name;
        this.language.comment = data.comment ? data.comment : '';
        this.language.competencyId = this.competencies.find(
          (item) => item.id === data.competency?.id,
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: `/api/v2/pim/employees/${this.employeeId}/languages/${this.data.languageId}/fluencies/${this.data.fluencyId}`,
          data: {
            competencyId: this.language.competencyId.id,
            comment: this.language.comment,
          },
        })
        .then(() => {
          return this.$toast.updateSuccess();
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
