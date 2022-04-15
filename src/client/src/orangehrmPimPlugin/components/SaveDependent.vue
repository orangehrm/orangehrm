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
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">{{
      $t('pim.add_dependent')
    }}</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="dependent.name"
              :label="$t('general.name')"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="dependent.relationshipType"
              type="select"
              :label="$t('pim.relationship')"
              :rules="rules.relationshipType"
              :options="relationshipOptions"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item v-if="showRelationship">
            <oxd-input-field
              v-model="dependent.relationship"
              :label="$t('pim.please_specify')"
              :rules="rules.relationship"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              v-model="dependent.dateOfBirth"
              :label="$t('pim.date_of_birth')"
              :rules="rules.dateOfBirth"
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
  </div>
  <oxd-divider />
</template>

<script>
import {
  required,
  validDateFormat,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

const dependentModel = {
  name: '',
  relationshipType: null,
  relationship: '',
  dateOfBirth: '',
};

export default {
  name: 'SaveDependent',

  props: {
    http: {
      type: Object,
      required: true,
    },
  },

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      dependent: {...dependentModel},
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        relationshipType: [required],
        relationship: [required, shouldNotExceedCharLength(100)],
        dateOfBirth: [validDateFormat()],
      },
      relationshipOptions: [
        {id: 'child', label: this.$t('pim.child')},
        {id: 'other', label: this.$t('pim.other')},
      ],
    };
  },

  computed: {
    showRelationship() {
      return this.dependent.relationshipType?.id == 'other';
    },
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.dependent,
          relationshipType: this.dependent.relationshipType?.id,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.dependent = {...dependentModel};
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
