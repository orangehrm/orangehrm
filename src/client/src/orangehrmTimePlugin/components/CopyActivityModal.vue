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
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">
        {{ $t('time.copy_activity') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <project-autocomplete
          v-model="project"
          :rules="rules.project"
          :only-allowed="false"
          :label="$t('time.project_name')"
          :exclude-project-ids="[projectId]"
          required
        />
      </oxd-form-row>
      <template v-if="activities && activities.length > 0">
        <oxd-divider />
        <oxd-grid :cols="2" class="orangehrm-activites-container">
          <oxd-grid-item v-for="activity in activities" :key="activity.id">
            <oxd-input-field
              v-model="selectedActivities"
              type="checkbox"
              :value="activity.id"
              :disabled="!activity.unique"
              :option-label="activity.name"
            />
          </oxd-grid-item>
        </oxd-grid>
      </template>

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
import {required, validSelection} from '@ohrm/core/util/validation/rules';
import ProjectAutocomplete from '@/orangehrmTimePlugin/components/ProjectAutocomplete.vue';

export default {
  name: 'CopyActivityModal',
  components: {
    'oxd-dialog': OxdDialog,
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
    const http = new APIService(window.appGlobal.baseUrl, '');
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
          validSelection,
          () => {
            if (this.activities !== null && this.activities.length === 0) {
              return this.$t('time.no_assigned_activities');
            } else if (
              Array.isArray(this.activities) &&
              this.selectedActivities.length === 0
            ) {
              const hasUnique = this.activities.find(
                (activity) => activity.unique === true,
              );
              return hasUnique
                ? this.$t('time.no_activities_selected')
                : this.$t('general.already_exists');
            } else {
              return true;
            }
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
            url: `/api/v2/time/projects/${this.projectId}/activities/copy/${value.id}`,
            params: {limit: 0},
          })
          .then((response) => {
            const {data} = response.data;
            this.activities = data;
            this.selectedActivities = Array.isArray(data)
              ? data
                  .filter((activity) => activity.unique === true)
                  .map((activity) => activity.id)
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
        .request({
          method: 'POST',
          url: `/api/v2/time/projects/${this.projectId}/activities/copy/${this.project.id}`,
          data: {
            activityIds: this.selectedActivities,
          },
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

<style lang="scss" scoped>
.orangehrm-activites-container {
  max-height: 180px;
  overflow-y: auto;
  @include oxd-scrollbar();
}
::v-deep(.oxd-checkbox-wrapper) {
  word-break: break-word;
  .oxd-checkbox-input {
    flex-shrink: 0;
  }
}
</style>
