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
      {{ titleLabel }}
    </oxd-text>

    <oxd-divider />

    <oxd-form @submit="emitVerify">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="password"
              name="password"
              label="Verify Password"
              placeholder="Password"
              type="password"
              :rules="rules.password"
            />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>
      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <div class="orangehrm-maintenance-note">
              <div class="orangehrm-maintenance-note-header">
                <oxd-text>Note</oxd-text>
              </div>
              <oxd-text>{{ noteText }}</oxd-text>
            </div>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />

      <oxd-form-actions>
        <oxd-button display-type="secondary" label="Verify" type="submit" />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
import {required} from '@/core/util/validation/rules';

export default {
  name: 'VerifyPassword',

  props: {
    titleLabel: {
      type: String,
      required: true,
    },
  },

  emits: ['verify'],

  data() {
    return {
      //TODO: get real instance identifier
      noteText:
        'Users who seek access to their data, or who seek to correct, amend, or delete the given information should direct their requests to Data@orangehrm.com with the subject "Purge Records (Instance Identifier : T0hSTV9kZXZpQHRlc3QuY29tX0RldmlfRFNfcGhwNzNfXzQuOV8xNjQ2MDE1NzQw REPLACE WITH REAL INSTANCE IDENTIFIER)"',
      password: '',
      rules: {
        password: [required],
      },
    };
  },

  methods: {
    emitVerify() {
      this.$emit('verify');
    },
  },
};
</script>

<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_colors.scss';
@import '@ohrm/oxd/styles/_variables.scss';

.orangehrm-maintenance-note {
  display: flex;
  flex-direction: column;
  overflow-wrap: break-word;
  padding: 1.2rem;
  border-radius: 1.2rem;
  color: $oxd-input-control-font-color;
  background-color: $oxd-background-light-gray-color;
  font-size: $oxd-input-control-font-size;

  &-header {
    font-weight: bold;
    margin-bottom: 0.4rem;
  }
}
</style>
