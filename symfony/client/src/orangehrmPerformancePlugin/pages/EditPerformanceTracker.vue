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
      <oxd-text tag="h6" class="orangehrm-main-title">
        Edit Performance Tracker
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
                label="Employee Name"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <reviewers-autocomplete
                v-model="tracker.reviewers"
                :rules="rules.reviewers"
                :excludeEmployee="tracker.employee"
                label="Reviewers"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            type="button"
            display-type="ghost"
            label="Cancel"
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
import {APIService} from '@/core/util/services/api.service';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import ReviewersAutoComplete from '@/orangehrmPerformancePlugin/components/ReviewersAutoComplete';
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
    'employee-autocomplete': EmployeeAutocomplete,
    'reviewers-autocomplete': ReviewersAutoComplete,
  },
  props: {
    performaceTrackerId: {
      type: String,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
        window.appGlobal.baseUrl,
        '/api/v2/performance/performance-tracker',
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
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.performaceTrackerId) //performace-tracker-id
      .then(response => {
        const {data} = response.data;
        this.tracker.id = data.id;
        this.tracker.name = data.trackerName;
        this.tracker.employee = data.employee
          ? {
              id: data.employee.empNumber,
              label: `${data.employee.firstName} ${data.employee.middleName} ${data.employee.lastName}`,
              isPastEmployee: data.employee.terminationId ? true : false,
            }
          : null;
        this.tracker.reviewers = data.reviewers.map(employee => {
          return {
            id: employee.empNumber,
            label: `${employee.firstName} ${employee.middleName} ${employee.lastName}`,
            isPastEmployee: employee.terminationId ? true : false,
          };
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/performance/addPerformanceTracker');
    },
    onSave() {
      this.isLoading = true;
      const payload = {
        trackerName: this.tracker.name.trim(),
        empNumber: this.tracker.employee.id,
        reviewers: this.tracker.reviewers.map(employee => employee.id),
      };
      this.http
        .update(this.performaceTrackerId, payload)
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          // go back
          this.onCancel();
        });
    },
  },
};
</script>
