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
        {{ $t('pim.edit_custom_field') }}
      </oxd-text>

      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <oxd-input-field
                v-model="customField.fieldName"
                :label="$t('pim.field_name')"
                :rules="rules.fieldName"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item>
              <oxd-input-field
                v-model="customField.screen"
                type="select"
                :label="$t('pim.screen')"
                :rules="rules.screen"
                :options="screenList"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-row>
          <oxd-grid :cols="2" class="orangehrm-full-width-grid">
            <oxd-grid-item class="organization-name-container">
              <oxd-input-field
                v-model="customField.fieldType"
                type="select"
                :label="$t('general.type')"
                :rules="rules.fieldType"
                :options="fieldTypeList"
                required
                :disabled="fieldInUse"
              />
            </oxd-grid-item>
            <oxd-grid-item v-if="isDropDownField">
              <oxd-input-field
                v-model="customField.extraData"
                :label="$t('pim.select_options')"
                :rules="rules.extraData"
                :required="isDropDownField"
              />
              <oxd-text tag="p" class="select-options-hint">
                {{ $t('pim.enter_allowed_options_separated_by_commas') }}
              </oxd-text>
            </oxd-grid-item>
          </oxd-grid>
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
    </div>
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@ohrm/core/util/services/api.service';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

const customFieldModel = {
  id: null,
  fieldName: '',
  screen: '',
  fieldType: '',
  extraData: '',
};

export default {
  props: {
    customFieldId: {
      type: Number,
      required: true,
    },
    screenList: {
      type: Array,
      required: true,
    },
    fieldTypeList: {
      type: Array,
      required: true,
    },
    fieldInUse: {
      type: Boolean,
      required: true,
    },
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/custom-fields',
    );
    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      customField: {...customFieldModel},
      rules: {
        fieldName: [required, shouldNotExceedCharLength(250)],
        screen: [required, shouldNotExceedCharLength(100)],
        fieldType: [required, shouldNotExceedCharLength(15)],
        extraData: [required, shouldNotExceedCharLength(250)],
      },
    };
  },

  computed: {
    isDropDownField() {
      return this.customField.fieldType?.id === 1;
    },
  },
  created() {
    this.isLoading = true;
    this.http
      .get(this.customFieldId)
      .then((response) => {
        const {data} = response.data;
        this.customField.fieldName = data.fieldName;
        if (data.screen !== '' && data.screen !== null) {
          this.customField.screen = this.screenList.find((c) => {
            return c.id === data.screen;
          });
        }
        if (data.fieldType !== '' && data.fieldType !== null) {
          this.customField.fieldType = this.fieldTypeList.find((c) => {
            return c.id === data.fieldType;
          });
        }
        this.customField.extraData = data.extraData;

        // Fetch list data for unique test
        return this.http.getAll();
      })
      .then((response) => {
        const {data} = response.data;
        this.rules.fieldName.push((v) => {
          const index = data.findIndex((item) => item.fieldName === v);
          if (index > -1) {
            const id = data[index].id;
            return id != this.customFieldId
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
        .update(this.customFieldId, {
          fieldName: this.customField.fieldName,
          screen: this.customField.screen.id,
          fieldType: this.customField.fieldType.id,
          extraData: this.customField.extraData,
        })
        .then(() => {
          return this.$toast.updateSuccess();
        })
        .then(() => {
          this.onCancel();
        });
    },
    onCancel() {
      navigate('/pim/listCustomFields');
    },
  },
};
</script>

<style src="./customField.scss" lang="scss" scoped></style>
