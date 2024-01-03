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
        {{ $t('admin.add_organization_unit') }}
      </oxd-text>
    </div>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
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
      <oxd-text tag="p" class="level-label">
        {{ $t('admin.this_unit_will_be_added_under') }}
        <b>
          {{ data?.unitId ? `${data.unitId}: ${data?.name}` : `${data?.name}` }}
        </b>
      </oxd-text>
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
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {OxdDialog} from '@ohrm/oxd';

const orgUnitModel = {
  unitId: '',
  name: '',
  description: '',
};

export default {
  name: 'SaveOrgUnit',
  components: {
    'oxd-dialog': OxdDialog,
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
      '/api/v2/admin/subunits',
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
      .getAll()
      .then((response) => {
        const {data} = response.data;
        if (data) {
          this.rules.name.push((v) => {
            const index = data.findIndex(
              (item) =>
                String(item.name).toLowerCase() == String(v).toLowerCase(),
            );
            if (index > -1) {
              return this.$t('admin.organization_unit_name_should_be_unique');
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
        .create({
          ...this.orgUnit,
          parentId: this.data?.id,
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      this.orgUnit = {...orgUnitModel};
      this.$emit('close', true);
    },
  },
};
</script>

<style scoped>
.level-label {
  font-size: 0.75rem;
}
</style>
