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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('attendance.attendance_configuration') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submitValid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <div class="orangehrm-attendance-field-row">
              <oxd-text tag="p" class="orangehrm-attendance-field-label">
                {{
                  $t(
                    'attendance.employee_can_change_current_time_when_punching_in_out',
                  )
                }}
              </oxd-text>
              <oxd-switch-input v-model="config.canUserChangeCurrentTime" />
            </div>
            <div class="orangehrm-attendance-field-row">
              <oxd-text tag="p" class="orangehrm-attendance-field-label">
                {{
                  $t(
                    'attendance.employee_can_edit_delete_own_attendance_records',
                  )
                }}
              </oxd-text>
              <oxd-switch-input v-model="config.canUserModifyAttendance" />
            </div>
            <div class="orangehrm-attendance-field-row">
              <oxd-text tag="p" class="orangehrm-attendance-field-label">
                {{
                  $t(
                    'attendance.supervisor_can_add_edit_delete_attendance_records_of_subordinates',
                  )
                }}
              </oxd-text>
              <oxd-switch-input
                v-model="config.canSupervisorModifyAttendance"
              />
            </div>
          </oxd-grid>
        </oxd-form-row>

        <oxd-divider />

        <oxd-form-actions>
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import SwitchInput from '@ohrm/oxd/core/components/Input/SwitchInput';

const configsModel = {
  canUserChangeCurrentTime: false,
  canUserModifyAttendance: false,
  canSupervisorModifyAttendance: false,
};

export default {
  components: {
    'oxd-switch-input': SwitchInput,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/attendance/configs',
    );
    return {
      http,
    };
  },
  data() {
    return {
      config: {...configsModel},
      isLoading: false,
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.config = {...data};
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
            ...this.config,
          },
        })
        .then(response => {
          const {data} = response.data;
          this.config = {...data};
          return this.$toast.saveSuccess();
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>
<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-attendance-field-row {
  grid-column-start: 1;
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
}
.orangehrm-attendance-field-label {
  @include oxd-input-control();
  padding: 0;
  flex-basis: 75%;
}
</style>
