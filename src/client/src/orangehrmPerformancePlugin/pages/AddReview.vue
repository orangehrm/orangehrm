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
        {{ $t('performance.add_review') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete
                v-model="review.employee"
                :rules="rules.employee"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <supervisor-autocomplete
                v-model="review.supervisorReviewer"
                :rules="rules.supervisorReviewer"
                required
                :subordinate="review.employee"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <date-input
                v-model="review.startDate"
                :label="$t('performance.review_period_start_date')"
                :rules="rules.startDate"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="review.endDate"
                :label="$t('performance.review_period_end_date')"
                :rules="rules.endDate"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <date-input
                v-model="review.dueDate"
                :label="$t('performance.due_date')"
                :rules="rules.dueDate"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <div class="orangehrm-button-row">
            <oxd-button
              display-type="ghost"
              :label="$t('general.cancel')"
              type="button"
              @click="onCancel"
            />
            <oxd-button
              display-type="ghost"
              :label="$t('general.save')"
              type="button"
              @click="onSave"
            />
            <oxd-button
              class="orangehrm-left-space"
              display-type="secondary"
              :label="$t('performance.activate')"
              type="button"
              @click="onActivate"
            />
          </div>
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete';
import SupervisorAutoComplete from '@/orangehrmPerformancePlugin/components/SupervisorAutoComplete';
import {APIService} from '@/core/util/services/api.service';
import {
  endDateShouldBeAfterStartDate,
  required,
  startDateShouldBeBeforeEndDate,
  validDateFormat,
} from '@/core/util/validation/rules';

const reviewModel = {
  employee: null,
  supervisorReviewer: null,
  startDate: null,
  endDate: null,
  dueDate: null,
};

export default {
  components: {
    'employee-autocomplete': EmployeeAutocomplete,
    'supervisor-autocomplete': SupervisorAutoComplete,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/reviews',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      review: {...reviewModel},
      rules: {
        employee: [required],
        supervisorReviewer: [required],
        startDate: [
          required,
          validDateFormat(),
          startDateShouldBeBeforeEndDate(
            () => this.review.endDate,
            this.$t(
              'general.review_period_start_date_should_be_before_end_date',
            ),
            {allowSameDate: false},
          ),
        ],
        endDate: [
          required,
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.review.startDate,
            this.$t(
              'performance.review_period_end_date_should_be_after_start_date',
            ),
          ),
        ],
        dueDate: [
          required,
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.review.endDate,
            this.$t(
              'performance.due_date_should_be_after_review_period_end_date',
            ),
          ),
        ],
      },
    };
  },
  methods: {
    onCancel() {
      navigate('/performance/searchPerformanceReview');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          empNumber: this.review.employee.id,
          reviewer: this.review.supervisorReviewer.id,
          startDate: this.review.startDate,
          endDate: this.review.endDate,
          dueDate: this.review.dueDate,
          activate: false,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          // go back
          this.onCancel();
        });
    },
    onActivate() {
      this.isLoading = true;
      this.http
        .create({
          empNumber: this.review.employee.id,
          reviewer: this.review.supervisorReviewer.id,
          startDate: this.review.startDate,
          endDate: this.review.endDate,
          dueDate: this.review.dueDate,
          activate: true,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .catch(error => {
          return this.$toast.warn({
            title: this.$t('general.warning'),
            message: error,
          });
        })
        .then(() => {
          // go back
          this.onCancel();
        });
    },
  },
};
</script>
<style src="./review.scss" lang="scss" scoped></style>
