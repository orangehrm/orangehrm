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
      {{ $t('general.edit') }} {{ type }}
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <report-to-employee-autocomplete
              v-model="reportTo.employee"
              required
              disabled
              :api="allowedEmployeesApi"
              :rules="rules.employee"
              :clear="false"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="reportTo.reportingMethod"
              type="select"
              :label="$t('pim.reporting_method')"
              :rules="rules.reportingMethod"
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
  validSelection,
} from '@ohrm/core/util/validation/rules';
import ReportToEmployeeAutocomplete from '@/orangehrmPimPlugin/components/ReportToEmployeeAutocomplete';

const reportToModel = {
  employee: null,
  reportingMethod: null,
};
export default {
  name: 'EditEmployeeReportTo',

  components: {
    'report-to-employee-autocomplete': ReportToEmployeeAutocomplete,
  },

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
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
    api: {
      type: String,
      required: true,
    },
  },

  emits: ['close'],
  setup(props) {
    const allowedEmployeesApi = `/api/v2/pim/employees/${props.empNumber}/report-to/allowed`;
    return {
      allowedEmployeesApi,
    };
  },

  data() {
    return {
      isLoading: false,
      reportTo: {...reportToModel},
      rules: {
        employee: [required, shouldNotExceedCharLength(100), validSelection],
        reportingMethod: [required],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .request({
        method: 'GET',
        url:
          this.type === 'Supervisor'
            ? `${this.api}${this.data.supervisorEmpNumber}`
            : `${this.api}${this.data.subordinateEmpNumber}`,
      })
      .then((response) => {
        const {data} = response.data;
        this.reportTo.employee = {
          id:
            this.type === 'Supervisor'
              ? data.supervisor.empNumber
              : data.subordinate.empNumber,
          label:
            this.type === 'Supervisor'
              ? `${data.supervisor.firstName} ${data.supervisor.middleName} ${data.supervisor.lastName}`
              : `${data.subordinate.firstName} ${data.subordinate.middleName} ${data.subordinate.lastName}`,
          isPastEmployee:
            this.type === 'Supervisor'
              ? data.supervisor.terminationId
              : data.subordinate.terminationId,
        };
        this.reportTo.reportingMethod = this.reportingMethods.find(
          (item) => item.id === data.reportingMethod.id,
        );
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const id =
        this.type === 'Supervisor'
          ? this.data.supervisorEmpNumber
          : this.data.subordinateEmpNumber;
      this.http
        .update(id, {
          reportingMethodId: this.reportTo.reportingMethod?.id,
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
