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
    <oxd-text tag="h6" class="orangehrm-main-title">Edit Supervisor</oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-dropdown v-model="reportTo.employee" />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
                type="dropdown"
                label="Reporting Method"
                v-model="reportTo.reportingMethod"
                :rules="rules.reportingMethod"
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
import {required} from "../../core/util/validation/rules";

const reportToModel = {
  employee: '',
  reportingMethod: '',
};

export default {
  name: 'edit-supervisor',

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
  },

  data() {
    return {
      isLoading: false,
      reportTo: {...reportToModel},
      rules: {
        employee: [required],
        reportingMethod: [required],
      },
    };
  },

  methods: {
    onSave() {
      console.log('On Save Called')
    },
    onCancel() {
      this.$emit('close', true);
    },
  },

  beforeMount() {
    this.isLoading = true;
  },
};
</script>
