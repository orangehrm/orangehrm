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
        Edit Key Performance Indicator
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
                v-model.number="kpi.minRate"
                label="Minimum Rating"
                required
                autcomplete="off"
                :rules="rules.minRate"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model.number="kpi.maxRate"
                label="Maximum Rating"
                required
                autcomplete="off"
                :rules="rules.maxRate"
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
  digitsOnly,
  shouldNotExceedCharLength,
  minValueShouldBeLowerThanMaxValue,
} from '@ohrm/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown.vue';

const initialKpi = {
  title: '',
  jobTitle: null,
  minRate: null,
  maxRate: null,
  isDefault: false,
};

const rateError = v => {
  if (digitsOnly(v) === true && v >= 0 && v <= 100) return true;
  return 'Should be a number between 0-100';
};

export default {
  name: 'KpiSave',
  components: {
    'oxd-switch-input': SwitchInput,
    'jobtitle-dropdown': JobtitleDropdown,
  },
  props: {
    kpiId: {
      type: String,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      'https://02594277-e771-4db3-a5ec-ad645ec49c02.mock.pstmn.io',
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
        minRate: [
          required,
          rateError,
          v =>
            !this.kpi.maxRate ||
            v <= this.kpi.maxRate ||
            'Minimum Rating should be less than Maximum Rating',
        ],
        maxRate: [
          required,
          rateError,
          minValueShouldBeLowerThanMaxValue(
            () => this.kpi.minRate,
            'Maximum Rating should be greater than Minimum Rating',
          ),
        ],
      },
    };
  },
  created() {
    this.isLoading = true;
    this.http
      .get(this.kpiId)
      .then(response => {
        const {data} = response.data;
        this.kpi.title = data.title;
        this.kpi.jobTitle = {id: data.jobTitle.id, label: data.jobTitle.title};
        this.kpi.minRate = data.minRate;
        this.kpi.maxRate = data.maxRate;
        this.kpi.isDefault = data.isDefault;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onCancel() {
      navigate('/performance/searchKpi');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.kpiId, {
          title: this.kpi.title,
          jobTitleId: this.kpi.jobTitle.id,
          minRate: this.kpi.minRate,
          maxRate: this.kpi.maxRate,
          isDefault: this.kpi.isDefault,
        })
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

<style src="./kpi.scss" lang="scss" scoped></style>
