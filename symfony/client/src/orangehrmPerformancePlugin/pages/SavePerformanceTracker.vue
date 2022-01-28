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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text class="orangehrm-main-title" tag="h6">
        Add Performance Tracker
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="tracker.name"
                :rules="rules.tracker"
                label="Tracker Name"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="tracker.employee"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <reviewers-autocomplete
                v-model="tracker.reviewers"
                :rules="rules.reviewers"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            label="Cancel"
            type="button"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import ReviewersAutoComplete from '@/orangehrmPerformancePlugin/components/ReviewersAutoComplete';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import {APIService} from '@/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';

const trackerModel = {
  name: null,
  employee: null,
  reviewers: [],
};

export default {
  components: {
    'reviewers-autocomplete': ReviewersAutoComplete,
    'employee-autocomplete': EmployeeAutocomplete,
  },
  setup() {
    const http = new APIService(
      'https://796aa478-538c-47e3-8133-bc2f05a479b1.mock.pstmn.io',
      '/api/v2/performance/savePerformanceTracker',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      tracker: {...trackerModel},
      rules: {
        tracker: [required, shouldNotExceedCharLength(200)],
        employee: [required],
        reviewers: [required],
      },
    };
  },
  methods: {
    onCancel() {
      navigate('/performance/addPerformanceTracker');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          name: this.tracker.name.trim(),
          employee: this.tracker.employee,
          reviewer: this.tracker.reviewers.map(employee => employee.id),
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          // go back
          this.onCancel();
        });
    },
  },
};
</script>
