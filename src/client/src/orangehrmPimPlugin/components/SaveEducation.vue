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
  <div class="orangehrm-horizontal-padding orangehrm-top-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('general.add_education') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <qualification-dropdown
              v-model="education.educationId"
              :label="$t('general.level')"
              :rules="rules.educationId"
              :api="api"
              required
            ></qualification-dropdown>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="education.institute"
              :label="$t('pim.institute')"
              :rules="rules.institute"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="education.major"
              :label="$t('pim.major_specialization')"
              :rules="rules.major"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="education.year"
              :label="$t('general.year')"
              :rules="rules.year"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="education.score"
              :label="$t('pim.gpa_score')"
              :rules="rules.score"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              v-model="education.startDate"
              :label="$t('general.start_date')"
              :rules="rules.startDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="education.endDate"
              :label="$t('general.end_date')"
              :rules="rules.endDate"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

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
    <oxd-divider />
  </div>
</template>

<script>
import QualificationDropdown from '@/orangehrmPimPlugin/components/QualificationDropdown';
import {
  required,
  shouldNotExceedCharLength,
  digitsOnly,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';

const educationModel = {
  educationId: null,
  institute: '',
  major: '',
  year: '',
  score: '',
  startDate: '',
  endDate: '',
};

export default {
  name: 'SaveEducation',

  components: {
    'qualification-dropdown': QualificationDropdown,
  },

  props: {
    http: {
      type: Object,
      required: true,
    },
    api: {
      type: String,
      required: true,
    },
  },

  emits: ['close'],

  setup() {
    const {userDateFormat} = useDateFormat();

    return {
      userDateFormat,
    };
  },

  data() {
    return {
      isLoading: false,
      education: {...educationModel},
      rules: {
        educationId: [required],
        institute: [shouldNotExceedCharLength(100)],
        major: [shouldNotExceedCharLength(100)],
        score: [shouldNotExceedCharLength(25)],
        year: [shouldNotExceedCharLength(4), digitsOnly],
        startDate: [validDateFormat(this.userDateFormat)],
        endDate: [
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(() => this.education.startDate),
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.education,
          educationId: this.education.educationId?.id,
          year: parseInt(this.education.year),
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
