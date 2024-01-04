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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('admin.add_work_shift') }}
      </oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="workShift.name"
                :label="$t('admin.shift_name')"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-divider />

        <oxd-form-row>
          <oxd-text class="orangehrm-sub-title"
            >{{ $t('admin.working_hours') }} *</oxd-text
          >
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <time-input
                v-model="workShift.startTime"
                :rules="rules.fromTime"
                :label="$t('general.from')"
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <time-input
                v-model="workShift.endTime"
                :rules="rules.endTime"
                :label="$t('general.to')"
              />
            </oxd-grid-item>

            <oxd-grid-item>
              <oxd-input-group :label="$t('admin.duration_per_day')">
                <oxd-text class="orangehrm-workshift-duration" tag="p">
                  {{ selectedTimeDuration }}
                </oxd-text>
              </oxd-input-group>
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <work-shift-employee-autocomplete
                v-model="workShift.empNumbers"
                :rules="rules.empNumbers"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  validSelection,
  validTimeFormat,
  shouldNotExceedCharLength,
  endTimeShouldBeAfterStartTime,
} from '@ohrm/core/util/validation/rules';
import {diffInTime} from '@/core/util/helper/datefns';
import useServerValidation from '@/core/util/composable/useServerValidation';
import WorkShiftEmployeeAutocomplete from '@/orangehrmAdminPlugin/components/WorkShiftEmployeeAutocomplete';

const workShiftModel = {
  id: '',
  name: '',
  hoursPerDay: '',
  startTime: '',
  endTime: '',
  empNumbers: [],
};
export default {
  components: {
    'work-shift-employee-autocomplete': WorkShiftEmployeeAutocomplete,
  },
  props: {
    workShiftConfig: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/work-shifts',
    );
    const {createUniqueValidator} = useServerValidation(http);
    const workShiftUniqueValidation = createUniqueValidator(
      'WorkShift',
      'name',
    );

    return {
      http,
      workShiftUniqueValidation,
    };
  },
  data() {
    return {
      isLoading: false,
      workShift: {...workShiftModel},
      rules: {
        name: [
          required,
          this.workShiftUniqueValidation,
          shouldNotExceedCharLength(50),
        ],
        fromTime: [required, validTimeFormat],
        endTime: [
          required,
          validTimeFormat,
          endTimeShouldBeAfterStartTime(
            () => this.workShift.startTime,
            this.$t('general.to_time_should_be_after_from_time'),
          ),
        ],
        empNumbers: [validSelection],
      },
    };
  },
  computed: {
    selectedTimeDuration() {
      return parseFloat(
        diffInTime(this.workShift.startTime, this.workShift.endTime) / 3600,
      ).toFixed(2);
    },
  },
  beforeMount() {
    this.workShift.endTime = this.workShiftConfig.endTime;
    this.workShift.startTime = this.workShiftConfig.startTime;
  },
  methods: {
    onSave() {
      this.isLoading = true;
      const payload = {
        name: this.workShift.name,
        hoursPerDay: this.selectedTimeDuration,
        startTime: this.workShift.startTime,
        endTime: this.workShift.endTime,
        empNumbers: this.workShift.empNumbers.map((employee) => employee.id),
      };
      this.http
        .create(payload)
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.workShift = {...workShiftModel};
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/admin/workShift');
    },
  },
};
</script>
<style src="./work-shift.scss" lang="scss" scoped></style>
