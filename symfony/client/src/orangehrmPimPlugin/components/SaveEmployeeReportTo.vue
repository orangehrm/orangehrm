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
  <div class="orangehrm-horizontal-padding orangehrm-top-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">Add {{ type }}</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <report-to-employee-dropdown
              v-model="reportTo.employee"
              :rules="rules.employee"
              :api="api"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              type="dropdown"
              label="Reporting Method"
              v-model="reportTo.reportingMethodId"
              :rules="rules.reportingMethodId"
              :clear="false"
              :options="reportingMethods"
              required
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
    <oxd-divider />
  </div>
</template>

<script>
import ReportToEmployeeDropdown from '@/orangehrmPimPlugin/components/ReportToEmployeeDropdown';
import {required} from '@orangehrm/core/util/validation/rules';

const reportToModel = {
  employee: [],
  reportingMethodId: [],
};

export default {
  name: 'save-employee-report-to',

  emits: ['close'],

  props: {
    http: {
      type: Object,
      required: true,
    },
    reportingMethods: {
      type: Array,
      required: true,
    },
    type: {
      type: String,
      required: true,
    },
    empNumber: {
      type: String,
      required: true,
    },
  },

  components: {
    'report-to-employee-dropdown': ReportToEmployeeDropdown,
  },

  data() {
    return {
      isLoading: false,
      reportTo: {...reportToModel},
      rules: {
        employee: [required],
        reportingMethodId: [required],
      },
    };
  },

  setup(props) {
    const api = `api/v2/pim/employees/${props.empNumber}/report-to/allowed`;
    return {
      api,
    };
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          empNumber: this.reportTo.employee.map(item => item.id)[0],
          reportingMethodId: this.reportTo.reportingMethodId.map(
            item => item.id,
          )[0],
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
