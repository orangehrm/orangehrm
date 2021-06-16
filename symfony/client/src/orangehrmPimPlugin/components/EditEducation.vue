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
    <oxd-text tag="h6" class="orangehrm-main-title">Edit Education</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Level"
              v-model="education.name"
              required
              readonly
              disabled
            />
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
            <oxd-input-field
              label="Start Date"
              v-model="education.startDate"
              :rules="rules.startDate"
              type="date"
              placeholder="yyyy-mm-dd"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="End Date"
              v-model="education.endDate"
              :rules="rules.endDate"
              type="date"
              placeholder="yyyy-mm-dd"
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
import {
  shouldNotExceedCharLength,
  digitsOnly,
  validDateFormat,
  afterDate,
} from '@orangehrm/core/util/validation/rules';

const educationModel = {
  name: '',
  institute: '',
  major: '',
  year: '',
  score: '',
  startDate: '',
  endDate: '',
};

export default {
  name: 'edit-education',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      education: {...educationModel},
      rules: {
        institute: [shouldNotExceedCharLength(100)],
        major: [shouldNotExceedCharLength(100)],
        score: [shouldNotExceedCharLength(25)],
        year: [shouldNotExceedCharLength(4), digitsOnly],
        startDate: [validDateFormat('yyyy-MM-dd')],
        endDate: [
          validDateFormat('yyyy-MM-dd'),
          value => {
            return (
              afterDate('yyyy-MM-dd', value, this.education.startDate) ||
              'End date should be after start date'
            );
          },
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          institute: this.education.institute,
          major: this.education.major,
          year: parseInt(this.education.year),
          score: this.education.score,
          startDate: this.education.startDate,
          endDate: this.education.endDate,
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

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.education.name = data.education.name;
        this.education.institute = data.institute;
        this.education.major = data.major;
        this.education.year = data.year ? data.year : '';
        this.education.score = data.score;
        this.education.startDate = data.startDate ? data.startDate : '';
        this.education.endDate = data.endDate ? data.endDate : '';
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
