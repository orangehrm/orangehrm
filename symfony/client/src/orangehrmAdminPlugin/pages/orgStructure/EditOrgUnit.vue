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
  <oxd-dialog @update:show="onCancel" :style="{minWidth: '50%'}">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title">Edit Organization Unit</oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field label="Unit Id" v-model="orgUnit.unitId" />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          label="Name"
          v-model="orgUnit.name"
          :rules="rules.name"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          type="textarea"
          label="Description"
          placeholder="Type description here"
          v-model="orgUnit.description"
        />
      </oxd-form-row>

      <oxd-divider />

      <oxd-form-actions>
        <oxd-button
          type="button"
          displayType="ghost"
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
import Dialog from '@orangehrm/oxd/core/components/Dialog/Dialog';
import {required} from '@orangehrm/core/util/validation/rules';

const orgUnitModel = {
  unitId: '',
  name: '',
  description: '',
};

export default {
  name: 'edit-org-unit',
  props: {
    data: {
      type: Object,
    },
  },
  components: {
    'oxd-dialog': Dialog,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/admin/subunits',
    );
    return {
      http,
    };
  },
  data() {
    return {
      isLoading: false,
      orgUnit: {...orgUnitModel},
      rules: {
        name: [required],
      },
    };
  },
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          ...this.orgUnit,
        })
        .then(() => {
          return this.$toast.success({
            title: 'Success',
            message: 'Organization unit updated successfully!',
          });
        })
        .then(() => {
          this.isLoading = false;
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.http
      .get(this.data.id)
      .then(response => {
        const {data} = response.data;
        this.orgUnit.name = data.name;
        this.orgUnit.description = data.description;
        this.orgUnit.unitId = data.unitId;
        // Fetch list data for unique test
        return this.http.getAll();
      })
      .then(response => {
        const {data} = response.data;
        if (data) {
          this.rules.name.push(v => {
            const index = data.findIndex(item => item.name == v);
            if (index > -1) {
              const {id} = data[index];
              return id != this.data.id
                ? 'Organization unit name should be unique'
                : true;
            } else {
              return true;
            }
          });
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
};
</script>

<style scoped>
.oxd-overlay {
  z-index: 1100 !important;
}
</style>
