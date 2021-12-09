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
  <oxd-dialog
    :style="{width: '90%', maxWidth: '600px'}"
    @update:show="onCancel"
  >
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('time.copy_activity') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <project-autocomplete
          v-model="project"
          :rules="rules.project"
          :label="$t('time.project_name')"
          required
        />
      </oxd-form-row>
      <template v-if="activities && activities.length > 0">
        <oxd-divider />
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-input-field
              v-for="activity in activities"
              :key="activity.id"
              v-model="selectedActivities"
              type="checkbox"
              :value="activity.id"
              :disabled="!activity.unique"
              :option-label="activity.name"
            />
          </oxd-grid>
        </oxd-form-row>
      </template>

      <oxd-divider />
      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          display-type="ghost"
          label="Cancel"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import {required} from '@ohrm/core/util/validation/rules';
import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';

export default {
  name: 'CopyActivityModal',
  components: {
    'oxd-dialog': Dialog,
    'project-autocomplete': ProjectAutocomplete,
  },
  props: {
    projectId: {
      type: Number,
      required: true,
    },
  },
  emits: ['close'],
  setup() {
    const http = new APIService(
      // window.appGlobal.baseUrl,
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      `api/v2/time/project`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      project: null,
      activities: null,
      selectedActivities: [],
      rules: {
        project: [
          required,
          () => {
            return this.activities === null ||
              this.selectedActivities.length !== 0
              ? true
              : 'No activities selected';
          },
        ],
      },
    };
  },
  watch: {
    project(value) {
      this.activities = null;
      this.selectedActivities = [];
      if (value) {
        this.isLoading = true;
        this.http
          .request({
            method: 'GET',
            url: `api/v2/time/project/${value.id}/activities`,
            params: {
              targetProjectId: this.projectId,
            },
          })
          .then(response => {
            const {data} = response.data;
            this.activities = data;
            this.selectedActivities = Array.isArray(data)
              ? data
                  .filter(activity => activity.unique === true)
                  .map(activity => activity.id)
              : [];
            this.isLoading = false;
          });
      }
    },
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.projectId, {
          activities: this.selectedActivities,
        })
        .then(() => {
          this.$toast.updateSuccess();
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
