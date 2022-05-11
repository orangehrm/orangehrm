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
              <status-input :status-id="shortlist.statusId" />
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
import StatusInput from '@/orangehrmRecruitmentPlugin/components/StatusInput';
export default {
  name: 'ShortlistCandidateScreen',
  components: {'status-input': StatusInput},
  setup() {
    const http = new APIService(
      'https://0d188518-fc5f-4b13-833d-5cd0e9fcef79.mock.pstmn.io',
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
        note: '',
        statusId: '',
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http.getAll().then(({data: {data}}) => {
      const {candidate, vacancy, manager, ...rest} = data;
      const {firstName, lastName, middleName} = candidate;
      const fullName = `${manager.firstName} ${manager.middleName} ${manager.lastName}`;
      this.shortlist = {
        candidate: `${firstName} ${middleName} ${lastName}`,
        vacancy: vacancy.title,
        manager:
          (manager?.terminationId ? this.$t('general.past_employee') : '') +
          fullName,
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
          navigate('/recruitment/VacancyStatus');
        });
    },
    onBack() {
      navigate(`/recruitment/addCandidate/${this.shortlist.cid}`);
    },
  },
};
</script>
