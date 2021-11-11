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
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">
      Add Immigration
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-group
              label="Document"
              :classes="{wrapper: '--gender-grouped-field'}"
            >
              <oxd-input-field
                type="radio"
                v-model="immigration.type"
                optionLabel="Passport"
                value="1"
              />
              <oxd-input-field
                type="radio"
                v-model="immigration.type"
                optionLabel="Visa"
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
              label="Number"
              v-model="immigration.number"
              :rules="rules.number"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="Issued Date"
              v-model="immigration.issuedDate"
              :rules="rules.issuedDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="Expiry Date"
              v-model="immigration.expiryDate"
              :years="yearArray"
              :rules="rules.expiryDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              label="Eligible Status"
              v-model="immigration.status"
              :rules="rules.status"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="select"
              label="Issued by"
              v-model="immigration.countryCode"
              :options="countries"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input
              label="Eligible Review Date"
              v-model="immigration.reviewDate"
              :rules="rules.reviewDate"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="textarea"
              label="Comments"
              placeholder="Type Comments here"
              v-model="immigration.comment"
              :rules="rules.comment"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          displayType="ghost"
          label="Cancel"
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
  name: 'save-immigration',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    countries: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      isLoading: false,
      immigration: {...immigrationModel},
      yearArray: [...yearRange()],
      rules: {
        number: [required, shouldNotExceedCharLength(30)],
        expiryDate: [
          validDateFormat(),
          endDateShouldBeAfterStartDate(
            () => this.immigration.issuedDate,
            'Expiry date should be after issued date',
          ),
        ],
        status: [shouldNotExceedCharLength(30)],
        reviewDate: [validDateFormat()],
        comment: [shouldNotExceedCharLength(250)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.immigration,
          countryCode: this.immigration.countryCode?.id,
        })
        .then(() => {
          return this.$toast.saveSuccess();
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
