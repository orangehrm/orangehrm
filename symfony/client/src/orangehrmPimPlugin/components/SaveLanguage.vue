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
  <div class="orangehrm-horizontal-padding orangehrm-top-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">Add Language</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <qualification-dropdown
              label="Language"
              v-model="language.languageId"
              :rules="rules.languageId"
              api="api/v2/admin/languages"
              required
            ></qualification-dropdown>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="dropdown"
              label="Fluency"
              v-model="language.fluencyId"
              :options="fluencies"
              :rules="rules.fluencyId"
              :clear="false"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="dropdown"
              label="Competency"
              v-model="language.competencyId"
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
              type="textarea"
              label="Comments"
              v-model="language.comment"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          displayType="ghost"
          label="Cancel"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
    <oxd-divider />
  </div>
</template>

<script>
import QualificationDropdown from '@/orangehrmPimPlugin/components/QualificationDropdown';
import {
  required,
  shouldNotExceedCharLength,
} from '@orangehrm/core/util/validation/rules';

const languageModel = {
  languageId: [],
  fluencyId: [],
  competencyId: [],
  comment: '',
};

export default {
  name: 'save-language',

  emits: ['close'],

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
  },

  components: {
    'qualification-dropdown': QualificationDropdown,
  },

  data() {
    return {
      isLoading: false,
      language: {...languageModel},
      rules: {
        languageId: [required],
        fluencyId: [required],
        competencyId: [required],
        comment: [shouldNotExceedCharLength(100)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          languageId: this.language.languageId.map(item => item.id)[0],
          fluencyId: this.language.fluencyId.map(item => item.id)[0],
          competencyId: this.language.competencyId.map(item => item.id)[0],
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
