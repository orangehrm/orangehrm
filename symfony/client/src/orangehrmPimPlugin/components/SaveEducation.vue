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
    <oxd-text tag="h6" class="orangehrm-main-title">Add Education</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <qualification-dropdown
              label="Level"
              v-model="education.educationId"
              :rules="rules.educationId"
              :api="api"
              required
            ></qualification-dropdown>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Institute"
              v-model="education.institute"
              :rules="rules.institute"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Major/Specialization"
              v-model="education.major"
              :rules="rules.major"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Year"
              v-model="education.year"
              :rules="rules.year"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="GPA/Score"
              v-model="education.score"
              :rules="rules.score"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              label="Start Date"
              v-model="education.startDate"
              :rules="rules.startDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="End Date"
              v-model="education.endDate"
              :rules="rules.endDate"
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
  digitsOnly,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';

const educationModel = {
  educationId: null,
  institute: '',
  major: '',
  year: '',
  score: '',
  startDate: '',
  endDate: '',
};

export default {
  name: 'save-education',

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
      education: {...educationModel},
      rules: {
        educationId: [required],
        institute: [shouldNotExceedCharLength(100)],
        major: [shouldNotExceedCharLength(100)],
        score: [shouldNotExceedCharLength(25)],
        year: [shouldNotExceedCharLength(4), digitsOnly],
        startDate: [validDateFormat()],
        endDate: [
          validDateFormat(),
          endDateShouldBeAfterStartDate(() => this.education.startDate),
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.education,
          educationId: this.education.educationId?.id,
          year: parseInt(this.education.year),
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
