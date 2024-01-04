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
  <div v-if="fields.length !== 0" class="orangehrm-custom-fields">
    <oxd-divider />
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">{{
        $t('pim.custom_fields')
      }}</oxd-text>
      <oxd-divider />
      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item v-for="field in fields" :key="field.id">
              <oxd-input-field
                v-model="customFieldsModel[field.model]"
                :type="field.type"
                :label="field.label"
                :options="field.extraData"
                :rules="rules.default"
                :disabled="!$can.update(`${screen}_custom_fields`)"
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <template v-if="$can.update(`${screen}_custom_fields`)">
          <oxd-divider />
          <oxd-form-actions>
            <submit-button />
          </oxd-form-actions>
        </template>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {shouldNotExceedCharLength} from '@ohrm/core/util/validation/rules';

const formatExtraData = (data) => {
  return typeof data === 'string'
    ? data
        .split(',')
        .map((item, i) => {
          return {id: i, label: item};
        })
        .filter((item) => item.label.trim() != '')
    : [];
};

export default {
  name: 'ProfileCustomFields',
  props: {
    employeeId: {
      type: String,
      required: true,
    },
    screen: {
      type: String,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/pim/employees/${props.employeeId}/custom-fields?screen=${props.screen}`,
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      fields: [],
      customFieldsModel: {},
      rules: {
        default: [shouldNotExceedCharLength(250)],
      },
    };
  },

  beforeMount() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data, meta} = response.data;
        this.customFieldsModel = {...data};
        if (meta.fields && meta.fields.length > 0) {
          this.fields = meta.fields.map((field) => {
            const extraData = formatExtraData(field.extraData);
            const model = `custom${field.id}`;
            if (field.fieldType == 1 && data[model]) {
              const selected = extraData.find((i) => i.label == data[model]);
              this.customFieldsModel[model] = selected || null;
            }
            return {
              id: field.id,
              label: field.fieldName,
              type: field.fieldType == 1 ? 'select' : 'input',
              model,
              extraData,
            };
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
        .request({
          method: 'PUT',
          url: `/api/v2/pim/employees/${this.employeeId}/custom-fields`,
          data: {...this.customFieldsModel},
          transformRequest: [
            (data) => {
              for (const key in data) {
                if (data[key]?.label) data[key] = data[key].label;
              }
              return JSON.stringify(data);
            },
          ],
        })
        .then(() => {
          this.isLoading = false;
          this.$toast.saveSuccess();
        });
    },
  },
};
</script>
