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
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('pim.edit_immigration') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-group label="Document" :classes="immigrationTypeClasses">
              <oxd-input-field
                v-model="immigration.type"
                type="radio"
                :option-label="$t('pim.passport')"
                value="1"
              />
              <oxd-input-field
                v-model="immigration.type"
                type="radio"
                :option-label="$t('pim.visa')"
                value="2"
              />
            </oxd-input-group>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="immigration.number"
              :label="$t('pim.number')"
              :rules="rules.number"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="immigration.issuedDate"
              :label="$t('pim.issued_date')"
              :rules="rules.issuedDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="immigration.expiryDate"
              :label="$t('general.expiry_date')"
              :years="yearArray"
              :rules="rules.expiryDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="immigration.status"
              :label="$t('pim.eligible_status')"
              :rules="rules.status"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="immigration.countryCode"
              type="select"
              :label="$t('pim.issued_by')"
              :options="countries"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              v-model="immigration.reviewDate"
              :label="$t('pim.eligible_review_date')"
              :rules="rules.reviewDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="immigration.comment"
              type="textarea"
              :label="$t('general.comments')"
              :placeholder="$t('general.type_comments_here')"
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
  </div>
  <oxd-divider />
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
  endDateShouldBeAfterStartDate,
} from '@ohrm/core/util/validation/rules';
import {yearRange} from '@ohrm/core/util/helper/year-range';
import useDateFormat from '@/core/util/composable/useDateFormat';

const immigrationModel = {
  number: '',
  issuedDate: '',
  expiryDate: '',
  type: 1,
  status: '',
  reviewDate: '',
  countryCode: null,
  comment: '',
};

export default {
  name: 'EditImmigration',

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
    countries: {
      type: Array,
      default: () => [],
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
      immigration: {...immigrationModel},
      yearArray: [...yearRange()],
      rules: {
        number: [required, shouldNotExceedCharLength(30)],
        expiryDate: [
          validDateFormat(this.userDateFormat),
          endDateShouldBeAfterStartDate(
            () => this.immigration.issuedDate,
            this.$t('pim.expiry_date_should_be_after_issued_date'),
          ),
        ],
        status: [shouldNotExceedCharLength(30)],
        issuedDate: [validDateFormat(this.userDateFormat)],
        reviewDate: [validDateFormat(this.userDateFormat)],
        comment: [shouldNotExceedCharLength(250)],
      },
      immigrationTypeClasses: {
        wrapper: '--gender-grouped-field',
        label: {
          'oxd-input-field-required': true,
        },
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then((response) => {
        const {data} = response.data;
        this.immigration = {...immigrationModel, ...data};
        this.immigration.countryCode = this.countries.find(
          (item) => item.id === data.country?.code,
        );
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
          number: this.immigration.number,
          issuedDate: this.immigration.issuedDate,
          expiryDate: this.immigration.expiryDate,
          type: this.immigration.type,
          status: this.immigration.status,
          reviewDate: this.immigration.reviewDate,
          comment: this.immigration.comment,
          countryCode: this.immigration.countryCode?.id,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.immigration = {...immigrationModel};
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
