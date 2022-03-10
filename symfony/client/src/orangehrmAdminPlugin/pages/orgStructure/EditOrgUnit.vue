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
      <oxd-text type="card-title">{{
        $t('admin.edit_organization_unit')
      }}</oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submitValid="onSave">
      <oxd-form-row>
        <oxd-input-field
          v-model="orgUnit.unitId"
          :label="$t('admin.unit_id')"
          :rules="rules.unitId"
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="orgUnit.name"
          :label="$t('general.name')"
          :rules="rules.name"
          required
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="orgUnit.description"
          type="textarea"
          :label="$t('general.description')"
          :placeholder="$t('general.type_description_here')"
          :rules="rules.description"
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
import Dialog from '@ohrm/oxd/core/components/Dialog/Dialog';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

const orgUnitModel = {
  unitId: '',
  name: '',
  description: '',
};

export default {
  name: 'EditOrgUnit',
  components: {
    'oxd-dialog': Dialog,
  },
  props: {
    data: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['close'],
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
        unitId: [shouldNotExceedCharLength(100)],
        name: [required, shouldNotExceedCharLength(100)],
        description: [shouldNotExceedCharLength(400)],
      },
    };
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
            const index = data.findIndex(
              item =>
                String(item.name).toLowerCase() == String(v).toLowerCase(),
            );
            if (index > -1) {
              const {id} = data[index];
              return id != this.data.id
                ? this.$t('admin.organization_unit_name_should_be_unique')
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
  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .update(this.data.id, {
          ...this.orgUnit,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>
