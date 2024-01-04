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
      <oxd-text class="orangehrm-main-title">
        {{ $t('performance.edit_key_performance_indicator') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="kpi.title"
                :label="$t('performance.key_performance_indicator')"
                required
                :rules="rules.title"
                autcomplete="off"
                :readonly="isReadOnly"
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
                :label="$t('performance.minimum_rating')"
                required
                autcomplete="off"
                :rules="rules.minRating"
                :readonly="isReadOnly"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model.number="kpi.maxRating"
                :label="$t('performance.maximum_rating')"
                required
                autcomplete="off"
                :rules="rules.maxRating"
                :readonly="isReadOnly"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <div class="orangehrm-module-field-row">
                <oxd-text tag="p" class="orangehrm-module-field-label">
                  {{ $t('performance.make_default_scale') }}
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
import {navigate} from '@ohrm/core/util/helper/navigation';
import {
  required,
  shouldNotExceedCharLength,
  minValueShouldBeLowerThanMaxValue,
  maxValueShouldBeGreaterThanMinValue,
  numberShouldBeBetweenMinAndMaxValue,
} from '@ohrm/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown.vue';
import {OxdSwitchInput} from '@ohrm/oxd';

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
    'oxd-switch-input': OxdSwitchInput,
    'jobtitle-dropdown': JobtitleDropdown,
  },
  props: {
    kpiId: {
      type: Number,
      required: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/performance/kpis',
    );

    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      kpi: {...initialKpi},
      isReadOnly: false,
      rules: {
        title: [required, shouldNotExceedCharLength(100)],
        jobTitle: [required],
        minRating: [
          required,
          numberShouldBeBetweenMinAndMaxValue(0, 100),
          minValueShouldBeLowerThanMaxValue(
            () => this.kpi.maxRating,
            this.$t(
              'performance.minimum_rating_should_be_less_than_maximum_rating',
            ),
          ),
        ],
        maxRating: [
          required,
          numberShouldBeBetweenMinAndMaxValue(0, 100),
          maxValueShouldBeGreaterThanMinValue(
            () => this.kpi.minRating,
            this.$t(
              'performance.maximum_rating_should_be_greater_than_minimum_rating',
            ),
          ),
        ],
      },
    };
  },
  created() {
    this.isLoading = true;
    this.http
      .get(this.kpiId)
      .then((response) => {
        const {data} = response.data;
        const {meta} = response.data;
        this.isReadOnly = meta.editable === false;
        this.kpi.title = data.title;
        this.kpi.jobTitle = !data.jobTitle.deleted
          ? {id: data.jobTitle.id, label: data.jobTitle.name}
          : null;
        this.kpi.minRating = data.minRating;
        this.kpi.maxRating = data.maxRating;
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
          minRating: this.kpi.minRating,
          maxRating: this.kpi.maxRating,
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
