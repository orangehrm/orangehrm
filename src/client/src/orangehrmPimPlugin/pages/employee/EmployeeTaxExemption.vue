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
  <edit-employee-layout :employee-id="empNumber" screen="tax">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('general.tax_exemptions') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-text class="orangehrm-sub-title" tag="h6">
          {{ $t('pim.federal_income_tax') }}
        </oxd-text>
        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.federalStatus"
                type="select"
                :label="$t('general.status')"
                :options="statuses"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.federalExemptions"
                :label="$t('pim.exemptions')"
                :rules="rules.federalExemptions"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-text class="orangehrm-sub-title" tag="h6">
          {{ $t('pim.state_income_tax') }}
        </oxd-text>
        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.taxState"
                type="select"
                :label="$t('general.state')"
                :options="provinces"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.stateStatus"
                type="select"
                :label="$t('general.status')"
                :options="statuses"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.stateExemptions"
                :label="$t('pim.exemptions')"
                :rules="rules.stateExemptions"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.unemploymentState"
                type="select"
                :label="$t('pim.unemployment_state')"
                :options="provinces"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="taxExemption.workState"
                type="select"
                :label="$t('pim.work_state')"
                :options="provinces"
                :disabled="!$can.update('tax_exemptions')"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <submit-button v-if="$can.update('tax_exemptions')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import {shouldNotExceedCharLength} from '@ohrm/core/util/validation/rules';

const taxExemptionModel = {
  federalStatus: null,
  federalExemptions: null,
  taxStateCode: [],
  stateStatus: null,
  stateExemptions: null,
  unemploymentStateCode: [],
  workStateCode: [],
};

export default {
  components: {
    'edit-employee-layout': EditEmployeeLayout,
  },

  props: {
    empNumber: {
      type: String,
      required: true,
    },
    provinces: {
      type: Array,
      default: () => [],
    },
    statuses: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.empNumber}/tax-exemption`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      taxExemption: {...taxExemptionModel},
      rules: {
        federalExemptions: [
          (v) => {
            return (
              v.match(/^\d*\.?\d*$/) !== null ||
              this.$t('general.should_be_a_positive_number')
            );
          },
          shouldNotExceedCharLength(2),
        ],
        stateExemptions: [
          (v) => {
            return (
              v.match(/^\d*\.?\d*$/) !== null ||
              this.$t('general.should_be_a_positive_number')
            );
          },
          shouldNotExceedCharLength(2),
        ],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        this.updateModel(response);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            federalStatus: this.taxExemption.federalStatus?.id,
            federalExemptions: this.taxExemption.federalExemptions,
            taxStateCode: this.taxExemption.taxState?.id,
            stateStatus: this.taxExemption.stateStatus?.id,
            stateExemptions: this.taxExemption.stateExemptions,
            unemploymentStateCode: this.taxExemption.unemploymentState?.id,
            workStateCode: this.taxExemption.workState?.id,
          },
        })
        .then((response) => {
          this.updateModel(response);
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateModel(response) {
      const {data} = response.data;
      this.taxExemption.federalExemptions = data.federalExemptions;
      this.taxExemption.stateExemptions = data.stateExemptions;
      this.taxExemption.taxState = this.provinces.find(
        (item) => item.id === data.taxState.code,
      );
      this.taxExemption.unemploymentState = this.provinces.find(
        (item) => item.id === data.unemploymentState.code,
      );
      this.taxExemption.workState = this.provinces.find(
        (item) => item.id === data.workState.code,
      );
      this.taxExemption.federalStatus = this.statuses.find(
        (item) => item.id === data.federalStatus,
      );
      this.taxExemption.stateStatus = this.statuses.find(
        (item) => item.id === data.stateStatus,
      );
    },
  },
};
</script>
