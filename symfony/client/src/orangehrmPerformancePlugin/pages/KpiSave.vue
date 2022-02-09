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
      <oxd-text class="orangehrm-main-title">
        Add Key Performance Indicator
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="kpi.title"
                label="Key Performance Indicator"
                required
                :rules="rules.title"
                autcomplete="off"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <jobtitle-dropdown
                v-model="kpi.jobTitle"
                required
                :rules="rules.jobTitle"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model.number="kpi.minRating"
                label="Minimum Rating"
                required
                autcomplete="off"
                :rules="rules.minRating"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model.number="kpi.maxRating"
                label="Maximum Rating"
                required
                autcomplete="off"
                :rules="rules.maxRating"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <div class="orangehrm-module-field-row">
                <oxd-text tag="p" class="orangehrm-module-field-label">
                  Make Default Scale
                </oxd-text>
                <oxd-switch-input v-model="kpi.isDefault" />
              </div>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <br />
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
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {
  required,
  shouldNotExceedCharLength,
  minValueShouldBeLowerThanMaxValue,
  maxValueShouldBeGreaterThanMinValue,
  numberShouldBeBetweenMinandMaxValue,
} from '@ohrm/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown.vue';

const initialKpi = {
  title: '',
  jobTitle: null,
  minRating: null,
  maxRating: null,
  isDefault: false,
};

export default {
  name: 'KpiSave',
  components: {
    'oxd-switch-input': SwitchInput,
    'jobtitle-dropdown': JobtitleDropdown,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/performance/kpi',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      kpi: {...initialKpi},
      rules: {
        title: [required, shouldNotExceedCharLength(100)],
        jobTitle: [required],
        minRating: [
          required,
          numberShouldBeBetweenMinandMaxValue(0, 100),
          minValueShouldBeLowerThanMaxValue(
            () => this.kpi.maxRating,
            'Minimum Rating should be less than Maximum Rating',
          ),
        ],
        maxRating: [
          required,
          numberShouldBeBetweenMinandMaxValue(0, 100),
          maxValueShouldBeGreaterThanMinValue(
            () => this.kpi.minRating,
            'Maximum Rating should be greater than Minimum Rating',
          ),
        ],
      },
    };
  },
  methods: {
    onCancel() {
      navigate('/performance/searchKpi');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          title: this.kpi.title,
          jobTitleId: this.kpi.jobTitle.id,
          minRating: this.kpi.minRating,
          maxRating: this.kpi.maxRating,
          isDefault: this.kpi.isDefault ? 1 : null,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
  },
};
</script>

<style src="./kpi.scss" lang="scss" scoped></style>
