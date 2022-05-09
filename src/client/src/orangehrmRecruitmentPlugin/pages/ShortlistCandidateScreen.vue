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
        {{ $t('recruitment.shortlist') }} {{ $t('recruitment.candidate') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid ">
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.candidate')"
                disabled
                :value="shortlist.candidate"
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
                :value="shortlist.vacancy"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.hiring_manager')"
                disabled
                :value="shortlist.manager"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                :label="$t('recruitment.current_status')"
                disabled
                :value="shortlist.status"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item
              class="orangehrm-save-candidate-page --span-column-2"
            >
              <oxd-input-field
                v-model="shortlist.note"
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
          <submit-button label="Shortlist" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@/core/util/helper/navigation';
export default {
  name: 'ShortlistCandidateScreen',
  setup() {
    const http = new APIService(
      'https://01eefc6d-daf1-4643-97ae-2d15ea8b587b.mock.pstmn.io',
      'recruitment/shortlistCandidate',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      shortlist: {
        candidate: '',
        vacancy: '',
        manager: '',
        status: '',
        note: '',
      },
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
      const {candidate, status, ...rest} = data;
      const {firstName, lastName, middleName} = candidate;
      const fullName = `${firstName} ${middleName} ${lastName}`;
      this.shortlist = {
        candidate: fullName,
        status: this.statuses.find(({id}) => id === status)?.label,
        cid: candidate.id,
        ...rest,
      };
      this.isLoading = false;
    });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.shortlist.id, {note: this.shortlist.note})
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          navigate('/recruitment/viewCandidates');
        });
    },
    onBack() {
      navigate(`/recruitment/addCandidate/${this.shortlist.cid}`);
    },
  },
};
</script>
