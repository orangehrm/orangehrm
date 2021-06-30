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
    <oxd-text tag="h6" class="orangehrm-main-title">Add Supervisor</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <qualification-dropdown
                label="Name"
                v-model="skill.skillId"
                :rules="rules.skillId"
                :api="api"
                required
            ></qualification-dropdown>
          </oxd-grid-item>
          <oxd-grid-item>
            <qualification-dropdown
                label="Reporting Method"
                v-model="skill.skillId"
                :rules="rules.skillId"
                :api="api"
                required
            ></qualification-dropdown>
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
  max,
  digitsOnly,
} from '@orangehrm/core/util/validation/rules';

const skillModel = {
  yearsOfExperience: 0,
  comments: '',
  skillId: [],
};

export default {
  name: 'save-supervisor',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    api: {
      type: String,
      required: true,
    },
  },

  components: {
    'qualification-dropdown': QualificationDropdown,
  },

  data() {
    return {
      isLoading: false,
      skill: {...skillModel},
      rules: {
        skillId: [required],
        yearsOfExperience: [digitsOnly, max(100)],
        comments: [shouldNotExceedCharLength(100)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
          .create({
            skillId: this.skill.skillId.map(item => item.id)[0],
            yearsOfExperience: parseInt(this.skill.yearsOfExperience),
            comments: this.skill.comments !== '' ? this.skill.comments : ' ',
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
