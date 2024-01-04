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
        {{ $t('time.edit_project_activity') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-input-field
          v-model="name"
          :label="$t('general.name')"
          :rules="rules.name"
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
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {APIService} from '@/core/util/services/api.service';
import {OxdDialog} from '@ohrm/oxd';

export default {
  name: 'SaveActivityModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    projectId: {
      type: Number,
      required: true,
    },
    activityId: {
      type: Number,
      required: true,
    },
  },
  emits: ['close'],
  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/time/project/${props.projectId}/activities`,
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      name: '',
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
      },
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.activityId)
      .then((response) => {
        const {data} = response.data;
        this.name = data.name;
        return this.http.getAll({limit: 0});
      })
      .then((response) => {
        const {data} = response.data;
        this.rules.name.push((v) => {
          const index = data.findIndex(
            (item) =>
              String(item.name).toLowerCase() == String(v).toLowerCase(),
          );
          if (index > -1) {
            const {id} = data[index];
            return id != this.activityId
              ? this.$t('general.already_exists')
              : true;
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.activityId, {
          name: this.name,
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
