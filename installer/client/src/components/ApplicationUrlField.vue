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
  <div>
    <oxd-input-field
      :model-value="modelValue"
      label="Application URL"
      :rules="rules"
      @update:model-value="$emit('update:modelValue', $event)"
    />
    <oxd-text tag="p" class="orangehrm-application-url-hint">
      Used in password reset emails. Enter the OrangeHRM address as shown in
      your browser before /auth/login, e.g.
      https://hr.example.com/orangehrm/index.php for
      https://hr.example.com/orangehrm/index.php/auth/login. Enter
      internationalised domain names in punycode form (xn--...).
      <template v-if="upgrader">
        <template v-if="stored">
          The stored URL is shown; check that it is still the address before
          /auth/login, especially if this instance was moved or copied.
        </template>
        If you leave it empty, the URL already stored (if any) is kept; without
        one, no password reset emails are sent until it is set with php
        bin/console orangehrm:set-base-url.
      </template>
      <template v-else>
        If you leave it empty, no password reset emails are sent until it is set
        with php bin/console orangehrm:set-base-url.
      </template>
    </oxd-text>
  </div>
</template>

<script>
import {validBaseUrl} from '@/core/util/validation/rules';

export default {
  name: 'ApplicationUrlField',
  props: {
    modelValue: {
      type: String,
      default: null,
    },
    upgrader: {
      type: Boolean,
      default: false,
    },
    stored: {
      type: String,
      default: null,
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      rules: [validBaseUrl],
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-application-url-hint {
  font-size: 0.75rem;
  margin-top: 0.25rem;
}
</style>
