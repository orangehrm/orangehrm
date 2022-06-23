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
        {{ $t('recruitment.view_action_history') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('recruitment.candidate')"
                  disabled
                  :value="schedule.candidate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('recruitment.vacancy')"
                  disabled
                  :value="schedule.vacancy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('recruitment.hiring_manager')"
                  disabled
                  :value="schedule.hiringManager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('recruitment.current_status')"
                  disabled
                  :value="schedule.status"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('recruitment.performed_action')"
                  disabled
                  :value="schedule.performedAction"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('general.performed_by')"
                  disabled
                  :value="schedule.performedBy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                  :label="$t('recruitment.performed_date')"
                  disabled
                  :value="getPerformedDate"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :rows ="2" :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page --span-column-2">
              <oxd-input-field
                  v-model="interview.note"
                  :label="$t('general.notes')"
                  type="textarea"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button
              display-type="ghost"
              :label="$t('general.back')"
              @click="onBack"
          />
          <submit-button :label="$t('general.save')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from "@/core/util/services/api.service";

const interviewModel = {
  candidate: null,
  vacancy: null,
  hiringManager: null,
  status: null,
  notes: null,
};

export default {
  name: 'ActionHistory',

  props: {
    candidateId: {
      type: Number,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
        window.appGlobal.baseUrl,
        '/api/v2/recruitment/candidates',
    );
    return {
      http,
    };
  },

  data(){
    return {
      isLoading: false,
      interview: {...interviewModel},
    }
  },
  beforeMount(){
    this.isLoading = true;
    this.http
        .get(this.candidateId)
        .then(response => {
          const {data} = response.data;
          this.interview.candidate = data.addedPerson;
        })
        .finally(() => {
          this.isLoading = false;
        });
  },
  methods: {
    onSave() {
      this.loading = true;
      const payload = {
        trackerName: this.tracker.name.trim(),
        empNumber: this.tracker.employee.id,
        reviewerEmpNumbers: this.tracker.reviewers.map(employee => employee.id),
      };
    }
  }
};
</script>
