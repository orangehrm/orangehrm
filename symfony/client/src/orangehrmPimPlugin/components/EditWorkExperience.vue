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
    <oxd-text tag="h6" class="orangehrm-main-title">
      Edit Work Experience
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              label="Company"
              v-model="workExperience.company"
              :rules="rules.company"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Job Title"
              v-model="workExperience.jobTitle"
              :rules="rules.jobTitle"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              label="From"
              v-model="workExperience.fromDate"
              :rules="rules.fromDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="To"
              v-model="workExperience.toDate"
              :rules="rules.toDate"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="--span-column-2">
            <oxd-input-field
              type="textarea"
              label="Comment"
              v-model="workExperience.comment"
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
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';

const workExpModel = {
  company: '',
  jobTitle: '',
  fromDate: '',
  toDate: '',
  comment: '',
};

export default {
  name: 'edit-work-experience',

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
      workExperience: {...workExpModel},
      rules: {
        company: [required, shouldNotExceedCharLength(100)],
        jobTitle: [required, shouldNotExceedCharLength(100)],
        fromDate: [validDateFormat()],
        toDate: [
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.workExperience.fromDate,
            'To date should be after From date',
          ),
        ],
        comment: [shouldNotExceedCharLength(200)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          ...this.workExperience,
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
        this.workExperience = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
