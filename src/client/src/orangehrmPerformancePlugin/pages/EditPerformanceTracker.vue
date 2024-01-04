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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('performance.edit_performance_tracker') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="tracker.name"
                :rules="rules.tracker"
                :label="$t('performance.tracker_name')"
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
                :readonly="isReadOnly"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <reviewer-autocomplete
                v-model="tracker.reviewers"
                :rules="rules.reviewers"
                :exclude-employee="tracker.employee"
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
            :label="$t('general.cancel')"
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
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import ReviewerAutoComplete from '@/orangehrmPerformancePlugin/components/ReviewerAutoComplete';
import {
  required,
  validSelection,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';

const trackerModel = {
  name: null,
  employee: null,
  reviewers: [],
};

export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'reviewer-autocomplete': ReviewerAutoComplete,
  },
  props: {
    performanceTrackerId: {
      type: Number,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/config/trackers',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      tracker: {...trackerModel},
      isReadOnly: false,
      rules: {
        tracker: [required, shouldNotExceedCharLength(200)],
        employee: [
          required,
          validSelection,
          (value) => {
            if (value === null) {
              return true;
            }
            const valid = this.tracker.reviewers.findIndex((reviewer) => {
              return reviewer.id === value.id;
            });
            if (valid == -1) {
              return true;
            }
            return this.$t(
              'performance.employee_cannot_be_assigned_as_his_own_reviewer',
            );
          },
        ],
        reviewers: [required, validSelection],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.performanceTrackerId) //performance-tracker-id
      .then((response) => {
        const {data} = response.data;
        this.isReadOnly = data.ownerEditable === false;
        this.tracker.id = data.id;
        this.tracker.name = data.trackerName;
        this.tracker.employee = data.employee
          ? {
              id: data.employee.empNumber,
              label: `${data.employee.firstName} ${data.employee.middleName} ${data.employee.lastName}`,
              isPastEmployee: data.employee.terminationId ? true : false,
            }
          : null;
        this.tracker.reviewers = data.reviewers.map((employee) => {
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
      navigate('/performance/viewPerformanceTracker');
    },
    onSave() {
      this.isLoading = true;
      const payload = {
        trackerName: this.tracker.name.trim(),
        empNumber: this.tracker.employee.id,
        reviewerEmpNumbers: this.tracker.reviewers.map(
          (employee) => employee.id,
        ),
      };
      this.http
        .update(this.performanceTrackerId, payload)
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },
};
</script>
