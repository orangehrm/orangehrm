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
    <oxd-text tag="h6" class="orangehrm-main-title">{{
      $t('general.add_license')
    }}</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <qualification-dropdown
              v-model="license.licenseId"
              :label="$t('pim.license_type')"
              :rules="rules.licenseId"
              :api="api"
              required
            ></qualification-dropdown>
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="license.licenseNo"
              :label="$t('pim.license_number')"
              :rules="rules.licenseNo"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input
              v-model="license.issuedDate"
              :label="$t('pim.issued_date')"
              :rules="rules.issuedDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="license.expiryDate"
              :label="$t('general.expiry_date')"
              :rules="rules.expiryDate"
              :years="yearArray"
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
  validDateFormat,
  shouldNotExceedCharLength,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';
import {yearRange} from '@ohrm/core/util/helper/year-range';
import useDateFormat from '@/core/util/composable/useDateFormat';

const licenseModel = {
  licenseId: null,
  licenseNo: '',
  issuedDate: '',
  expiryDate: '',
};

export default {
  name: 'SaveLicense',

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
      license: {...licenseModel},
      yearArray: [...yearRange()],
      rules: {
        licenseId: [required],
        licenseNo: [shouldNotExceedCharLength(50)],
        issuedDate: [validDateFormat(this.userDateFormat)],
        expiryDate: [
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(
            () => this.license.issuedDate,
            this.$t('pim.expiry_date_should_be_after_issued_date'),
          ),
        ],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.license,
          licenseId: this.license.licenseId?.id,
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
