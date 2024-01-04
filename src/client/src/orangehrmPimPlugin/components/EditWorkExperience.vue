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
      Edit Work Experience
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="workExperience.company"
              :label="$t('pim.company')"
              :rules="rules.company"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="workExperience.jobTitle"
              :label="$t('general.job_title')"
              :rules="rules.jobTitle"
              required
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              v-model="workExperience.fromDate"
              :label="$t('general.from')"
              :rules="rules.fromDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="workExperience.toDate"
              :label="$t('general.to')"
              :rules="rules.toDate"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item class="--span-column-2">
            <oxd-input-field
              v-model="workExperience.comment"
              type="textarea"
              :label="$t('general.comment')"
              :rules="rules.comment"
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
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';

const workExpModel = {
  company: '',
  jobTitle: '',
  fromDate: '',
  toDate: '',
  comment: '',
};

export default {
  name: 'EditWorkExperience',

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
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
      workExperience: {...workExpModel},
      rules: {
        company: [required, shouldNotExceedCharLength(100)],
        jobTitle: [required, shouldNotExceedCharLength(100)],
        fromDate: [validDateFormat(this.userDateFormat)],
        toDate: [
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(
            () => this.workExperience.fromDate,
            this.$t('general.to_date_should_be_after_from_date'),
          ),
        ],
        comment: [shouldNotExceedCharLength(200)],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then((response) => {
        const {data} = response.data;
        this.workExperience = {...data};
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          ...this.workExperience,
        })
        .then(() => {
          return this.$toast.updateSuccess();
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
