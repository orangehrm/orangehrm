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
              <oxd-input-field label="Candidate" disabled value="test" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field label="Vacancy" disabled value="test" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field label="Hiring Manager" disabled value="test" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field label="Current Status" disabled value="test" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field label="Performed Action" disabled value="test" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field label="Performed By" disabled value="test" />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field label="performed Date" disabled value="test" />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field label="Notes" type="textarea" :rules="rules.notes"/>
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
import {APIService} from "@/core/util/services/api.service";
export default {
  name: 'ShortlistHistoryScreen',
  setup(){
    const http = new APIService(
      'https://01eefc6d-daf1-4643-97ae-2d15ea8b587b.mock.pstmn.io',
      'recruitment/api/candidateHistory',
    );
    return {
      http,
    }
  },
  data() {
    return {
      isLoading: false,
      rules: {
        notes: [shouldNotLessThanCharLength(250)],
      },
      history:{
        candidate:"",
        vacancy:"",
        hiringManager:"",
        status:"",
        performedAction:"",
        performedBy:"",
        performedDate:""
      },
    };
  },
  beforeMount() {
    this.http.getAll().then(({data:{data}}) => {
      console.log(data);
      const {firstName, lastName, middleName} = data.candidate;
      history.candidate = `${firstName} ${middleName} ${lastName}`;
    });
  },
  methods: {
    onSave() {
      console.log('sa');
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
