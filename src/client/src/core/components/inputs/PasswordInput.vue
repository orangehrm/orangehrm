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
  <!-- Always use inside OXD-Form -->
  <oxd-form-row class="user-password-row">
    <oxd-grid :cols="2" class="orangehrm-full-width-grid">
      <oxd-grid-item class="user-password-cell">
        <password-strength-indicator
          v-if="password"
          :password-strength="passwordStrength"
        >
        </password-strength-indicator>
        <oxd-input-field
          type="password"
          autocomplete="off"
          :required="isPasswordRequired"
          :model-value="password"
          :rules="rules.password"
          :label="$t('general.password')"
          @update:model-value="$emit('update:password', $event)"
        />
        <oxd-text class="user-password-hint" tag="p">
          {{ $t('general.password_strength_message') }}
        </oxd-text>
      </oxd-grid-item>

      <oxd-grid-item>
        <oxd-input-field
          ref="passwordConfirm"
          type="password"
          autocomplete="off"
          :required="isPasswordRequired"
          :model-value="passwordConfirm"
          :rules="rules.passwordConfirm"
          :label="$t('general.confirm_password')"
          @update:model-value="$emit('update:passwordConfirm', $event)"
        />
      </oxd-grid-item>
    </oxd-grid>
  </oxd-form-row>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {promiseDebounce} from '@ohrm/oxd';
import {APIService} from '@/core/util/services/api.service';
import usePasswordPolicy from '@/core/util/composable/usePasswordPolicy';
import PasswordStrengthIndicator from '@/core/components/labels/PasswordStrengthIndicator';

export default {
  name: 'PasswordInput',
  components: {
    'password-strength-indicator': PasswordStrengthIndicator,
  },
  props: {
    password: {
      type: String,
      required: true,
    },
    passwordConfirm: {
      type: String,
      required: true,
    },
    isPasswordRequired: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['update:password', 'update:passwordConfirm'],
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '');
    const {passwordStrength, validatePassword} = usePasswordPolicy(http);

    return {
      passwordStrength,
      validatePassword,
    };
  },
  data() {
    return {
      rules: {
        password: [
          (v) => (this.isPasswordRequired ? required(v) : true),
          shouldNotExceedCharLength(64),
          promiseDebounce(this.validatePassword, 500),
        ],
        passwordConfirm: [
          (v) => {
            if (this.isPasswordRequired || this.password.length > 0) {
              return (
                (!!v && v === this.password) ||
                this.$t('general.passwords_do_not_match')
              );
            } else {
              return true;
            }
          },
        ],
      },
    };
  },

  watch: {
    password(value) {
      if (
        (!!this.passwordConfirm && value !== this.passwordConfirm) ||
        (!!this.passwordConfirm && value === this.passwordConfirm)
      ) {
        this.$nextTick(this.$refs.passwordConfirm.triggerUpdate);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.user-password {
  &-row {
    padding: 10px;
    background-color: $oxd-background-white-shadow-color;
    border-radius: 0.75rem;
    ::v-deep(.orangehrm-password-chip) {
      top: -5px;
      right: 8px;
    }
  }
  &-hint {
    font-size: 0.75rem;
  }
  &-cell {
    position: relative;
  }
}
</style>
