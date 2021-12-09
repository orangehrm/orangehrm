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
  <div class="orangehrm-card-container">
    <oxd-text tag="h6" class="orangehrm-main-title">
      {{ $t('time.timesheet_action') }}
    </oxd-text>
    <oxd-divider />
    <oxd-form @submitValid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="comment"
              type="textarea"
              placeholder="Type here"
              :rules="rules.comment"
              :label="$t('general.comment')"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />
      <oxd-form-actions>
        <submit-button :label="$t('general.approve')" />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
import {required} from '@/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'SaveTimesheetAction',

  props: {
    timesheetId: {
      type: Number,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      //   window.appGlobal.baseUrl,
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      '/api/v2/time/timesheet-actions',
    );

    return {
      http,
    };
  },
  data() {
    return {
      comment: null,
      rules: {
        comment: [required],
      },
    };
  },

  methods: {
    onSave() {
      this.http
        .update(this.timesheetId, {
          comment: this.comment,
          action: 'approve',
        })
        .then(() => {
          return this.$toast.updateSuccess();
        });
    },
  },
};
</script>
