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
        {{ $t('general.subscribe') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <oxd-form-row>
          <oxd-grid :cols="3" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <oxd-input-field
                v-model="subscriber.name"
                :label="$t('general.name')"
                :rules="rules.name"
                required
              />
            </oxd-grid-item>
            <oxd-grid-item class="--offset-row-2">
              <oxd-input-field
                v-model="subscriber.email"
                :label="$t('general.email')"
                :rules="rules.email"
                required
              />
            </oxd-grid-item>
          </oxd-grid>
          <oxd-text class="orangehrm-input-hint" tag="p">
            {{ $t('general.subscribe_notice') }}
          </oxd-text>
        </oxd-form-row>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <submit-button :label="$t('general.subscribe')" />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {ref} from 'vue';
import {
  required,
  validEmailFormat,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

export default {
  props: {
    name: {
      type: String,
      default: null,
    },
    email: {
      type: String,
      default: null,
    },
  },

  setup(props) {
    const rules = {
      name: [required, shouldNotExceedCharLength(50)],
      email: [required, validEmailFormat, shouldNotExceedCharLength(50)],
    };

    const isLoading = ref(false);
    const subscriber = ref({
      name: props.name,
      email: props.email,
    });

    const onSave = () => {
      // TODO: Implement subscribe action
    };

    return {
      rules,
      onSave,
      isLoading,
      subscriber,
    };
  },
};
</script>
