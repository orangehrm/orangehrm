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
  <edit-employee-layout :employee-id="empNumber" screen="tax">
    <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
      <oxd-text tag="h6" class="orangehrm-main-title">
        Tax Exemptions
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-text class="orangehrm-sub-title" tag="h6">
          Federal Income Tax
        </oxd-text>
        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Status"
                v-model="taxExemption.federalStatus"
                :options="status"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Exemptions"
                v-model="taxExemption.federalExemptions"
                :rules="rules.federalExemptions"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-text class="orangehrm-sub-title" tag="h6">
          State Income Tax
        </oxd-text>
        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="State"
                v-model="taxExemption.taxStateCode"
                :options="provinces"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Status"
                v-model="taxExemption.stateStatus"
                :options="status"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                label="Exemptions"
                v-model="taxExemption.stateExemptions"
                :rules="rules.stateExemptions"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Unemployment State"
                v-model="taxExemption.unemploymentStateCode"
                :options="provinces"
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                type="select"
                label="Work State"
                v-model="taxExemption.workStateCode"
                :options="provinces"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </edit-employee-layout>
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
import EditEmployeeLayout from '@/orangehrmPimPlugin/components/EditEmployeeLayout';
import {
  positiveNumber,
  lessthancharaters,
} from '@orangehrm/core/util/validation/rules';

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
    status: {
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
        federalExemptions: [positiveNumber, lessthancharaters(2)],
        stateExemptions: [positiveNumber, lessthancharaters(2)],
      },
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          data: {
            //...this.taxExemption,
            // membershipId: this.membership.membership.id,
            federalStatus: this.taxExemption.federalStatus,
            federalExemptions: this.taxExemption.federalExemptions,
            taxStateCode: this.taxExemption.taxState?.code,
            stateStatus: this.taxExemption.stateStatus,
            stateExemptions: this.taxExemption.stateExemptions,
            unemploymentStateCode: this.taxExemption.unemploymentState?.code,
            workStateCode: this.taxExemption.workState?.Code,
          },
        })
        .then(response => {
          this.updateModel(response);
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.isLoading = false;
        });
    },

    updateModel(response) {
      const {data} = response.data;
      //this.taxExemption = {...taxExemptionModel, ...data};
      this.taxExemption.federalExemptions = data.federalExemptions;
      this.taxExemption.stateExemptions = data.stateExemptions;
      this.taxExemption.taxStateCode = this.provinces.find(
        item => item.id === data.taxState.code,
      );
      this.taxExemption.unemploymentStateCode = this.provinces.find(
        item => item.id === data.unemploymentState.code,
      );
      this.taxExemption.workStateCode = this.provinces.find(
        item => item.id === data.workState.code,
      );
      this.taxExemption.federalStatus = this.status.find(
        item => item.id === data.federalStatus,
      );
      this.taxExemption.stateStatus = this.status.find(
        item => item.id === data.stateStatus,
      );
      //  this.membership.membership = this.memberships.find(
      //       item => item.id === data.membership.id,
      //     );
    },
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        this.updateModel(response);
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>
