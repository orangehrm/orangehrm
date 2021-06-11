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
    <oxd-text tag="h6">Add Dependent</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Name"
              v-model="dependent.name"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="dropdown"
              label="Relationship"
              v-model="dependent.relationshipType"
              :rules="rules.relationshipType"
              :clear="false"
              :options="relationshipOptions"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item v-if="showRelationship">
            <oxd-input-field
              label="Please Specify"
              v-model="dependent.relationship"
              :rules="rules.relationship"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Date of Birth"
              v-model="dependent.dateOfBirth"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-actions>
        <oxd-button
          type="button"
          displayType="ghost"
          label="Cancel"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </div>
  <oxd-divider />
</template>

<script>
import {required} from '@orangehrm/core/util/validation/rules';

const dependentModel = {
  name: '',
  relationshipType: [{id: 'child', label: 'Child'}],
  relationship: '',
  dateOfBirth: '',
};

export default {
  name: 'save-dependent',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      dependent: {...dependentModel},
      rules: {
        name: [
          required,
          v => {
            return !v || v.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
        relationshipType: [
          v => {
            return (!!v && v.length !== 0) || 'Required';
          },
        ],
        relationship: [
          required,
          v => {
            return !v || v.length <= 100 || 'Should not exceed 100 characters';
          },
        ],
      },
      relationshipOptions: [
        {id: 'child', label: 'Child'},
        {id: 'other', label: 'Other'},
      ],
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.dependent,
          relationshipType: this.dependent.relationshipType.map(
            item => item.id,
          )[0],
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Successfully Added',
          });
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

  computed: {
    showRelationship() {
      return this.dependent.relationshipType[0]?.id == 'other';
    },
  },
};
</script>
