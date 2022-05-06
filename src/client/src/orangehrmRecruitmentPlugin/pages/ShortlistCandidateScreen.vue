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
        Shortlist Candidate
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
            <oxd-grid-item class="orangehrm-save-candidate-page-full-width">
              <oxd-input-field label="notes" type="textarea" />
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
export default {
  name: 'ShortlistCandidateScreen',
  setup() {
    return {
      isLoading: false,
    };
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
