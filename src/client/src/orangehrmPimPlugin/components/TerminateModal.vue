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
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel(false)">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('pim.terminate_employment') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <date-input
          v-model="termination.date"
          :label="$t('pim.termination_date')"
          :rules="rules.date"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="termination.terminationReason"
          type="select"
          :label="$t('pim.termination_reason')"
          :rules="rules.terminationReason"
          :options="terminationReasons"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="termination.note"
          type="textarea"
          :label="$t('general.note')"
          :placeholder="$t('general.type_here')"
          :rules="rules.note"
        />
      </oxd-form-row>
      <oxd-divider />

      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel(false)"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {OxdDialog} from '@ohrm/oxd';
import {
  required,
  shouldNotExceedCharLength,
  validDateFormat,
} from '@ohrm/core/util/validation/rules';
import useDateFormat from '@/core/util/composable/useDateFormat';

const terminationModel = {
  terminationReason: null,
  date: '',
  note: null,
};

export default {
  name: 'TerminateModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    employeeId: {
      type: String,
      required: true,
    },
    terminationReasons: {
      type: Array,
      required: true,
    },
    terminationId: {
      type: Number,
      required: false,
      default: null,
    },
  },
  emits: ['close'],
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.employeeId}/terminations`,
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
      termination: {...terminationModel},
      rules: {
        terminationReason: [required],
        date: [required, validDateFormat(this.userDateFormat)],
        note: [shouldNotExceedCharLength(250)],
      },
    };
  },

  beforeMount() {
    if (this.terminationId) {
      this.isLoading = true;
      this.http
        .get(this.terminationId)
        .then((response) => {
          const {data} = response.data;
          this.termination.terminationReason = this.terminationReasons.find(
            (item) => item.id === data.terminationReason?.id,
          );
          this.termination.date = data.date;
          this.termination.note = data.note;
        })
        .finally(() => {
          this.isLoading = false;
        });
    }
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        date: this.termination.date,
        note: this.termination.note,
        terminationReasonId: this.termination.terminationReason?.id,
      };
      this.submitData(payload, this.terminationId)
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel(true);
        });
    },
    async submitData(payload, id) {
      return !id ? this.http.create(payload) : this.http.update(id, payload);
    },
    onCancel(reload) {
      this.$emit('close', reload);
    },
  },
};
</script>
