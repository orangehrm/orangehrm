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
  <oxd-dialog
    :style="{width: '90%', maxWidth: '450px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('time.add_timesheet') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <date-input
          v-model="date"
          :label="$t('time.select_a_day_to_create_timesheet')"
          :rules="rules.date"
          required
        />
      </oxd-form-row>
      <oxd-divider />

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
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {OxdDialog} from '@ohrm/oxd';
import {required, validDateFormat} from '@ohrm/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';

export default {
  name: 'AddTimesheetModal',
  components: {
    'oxd-dialog': OxdDialog,
  },

  props: {
    employeeId: {
      type: Number,
      required: false,
      default: null,
    },
  },
  emits: ['close'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/time/add-timesheet`,
    );
    const {userDateFormat} = useDateFormat();

    return {
      http,
      userDateFormat,
    };
  },
  data() {
    return {
      isLoading: false,
      date: null,
      rules: {
        date: [required, validDateFormat(this.userDateFormat)],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          date: this.date,
          employeeId: this.employeeId,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close');
    },
  },
};
</script>
