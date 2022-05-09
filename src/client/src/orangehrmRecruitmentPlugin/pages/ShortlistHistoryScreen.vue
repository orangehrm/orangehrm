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
  <div class="orangehrm-background-container orangehrm-save-candidate-page">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        View Action History
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Candidate"
                disabled
                :value="history.candidate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Vacancy"
                disabled
                :value="history.vacancy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Hiring Manager"
                disabled
                :value="history.manager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Current Status"
                disabled
                :value="history.status"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                label="Performed Action"
                disabled
                :value="history.performedAction"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Performed By"
                disabled
                :value="history.performedBy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="performed Date"
                disabled
                :value="history.performedDate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field
                label="Notes"
                type="textarea"
                :rules="rules.notes"
                :v-model="history.note"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button display-type="ghost" :label="$t('general.back')" />
          <submit-button label="Shortlist" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {shouldNotLessThanCharLength} from '@ohrm/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
export default {
  name: 'ShortlistHistoryScreen',
  setup() {
    const http = new APIService(
      'https://01eefc6d-daf1-4643-97ae-2d15ea8b587b.mock.pstmn.io',
      'recruitment/candidateHistory',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      rules: {
        notes: [shouldNotLessThanCharLength(250)],
      },
      history: {
        candidate: '',
        vacancy: '',
        manager: '',
        status: '',
        performedAction: '',
        performedBy: '',
        performedDate: '',
        note: '',
      },
      actions: [
        {
          id: 1,
          label: 'Shortlist',
        },
        {
          id: 2,
          label: 'Schedule Interview',
        },
        {
          id: 3,
          label: 'Mark Interview',
        },
        {
          id: 4,
          label: 'Passed',
        },
        {
          id: 5,
          label: 'Failed',
        },
        {
          id: 6,
          label: 'Offer Job',
        },
        {
          id: 7,
          label: 'Decline Offer',
        },
        {
          id: 7,
          label: 'Hire',
        },
        {
          id: 8,
          label: 'Reject',
        },
      ],
      statuses: [
        {
          id: 1,
          label: this.$t('recruitment.application_initiated'),
        },
        {
          id: 2,
          label: this.$t('recruitment.shortlisted'),
        },
        {
          id: 3,
          label: this.$t('recruitment.interview_scheduled'),
        },
        {
          id: 4,
          label: this.$t('recruitment.interview_passed'),
        },
        {
          id: 5,
          label: this.$t('recruitment.interview_failed'),
        },
        {
          id: 6,
          label: this.$t('recruitment.job_offered'),
        },
        {
          id: 7,
          label: this.$t('recruitment.offered_declined'),
        },
      ],
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then(({data: {data}}) => {
      const {candidate, status, performedAction, ...rest} = data;
      const {firstName, lastName, middleName} = candidate;
      const fullName = `${firstName} ${middleName} ${lastName}`;
      this.history = {
        candidate: fullName,
        status: this.statuses.find(({id}) => id === status)?.label,
        performedAction: this.actions.find(({id}) => id === performedAction)
          ?.label,
        ...rest,
      };
      this.isLoading = false;
    });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.history.id, {note: this.history.note})
        .then(() => {
          this.isLoading = false;
          return this.$toast.updateSuccess();
        })
        .then(() => {
          navigate('/recruitment/viewCandidates');
        });
    },
  },
};
</script>
<style scoped lang="scss">
.orangehrm-save-candidate-page {
  &-full-width {
    grid-column: 1 / span 2;
  }
}
</style>
