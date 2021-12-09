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
  <!-- Always use inside OXD-Form -->
  <oxd-form-row class="user-password-row">
    <oxd-grid :cols="2" class="orangehrm-full-width-grid">
      <oxd-grid-item class="user-password-cell">
        <oxd-chip
          v-if="password"
          :class="chipClasses"
          :label="passwordStrength"
        />
        <oxd-input-field
          label="Password"
          type="password"
          :model-value="password"
          :rules="rules.password"
          autocomplete="off"
          required
          @update:modelValue="$emit('update:password', $event)"
        />
        <oxd-text class="user-password-hint" tag="p">
          For a strong password, please use a hard to guess combination of text
          with upper and lower case characters, symbols and numbers
        </oxd-text>
      </oxd-grid-item>

      <oxd-grid-item>
        <oxd-input-field
          ref="passwordConfirm"
          label="Confirm Password"
          type="password"
          :model-value="passwordConfirm"
          :rules="rules.passwordConfirm"
          autocomplete="off"
          required
          @update:modelValue="$emit('update:passwordConfirm', $event)"
        />
      </oxd-grid-item>
    </oxd-grid>
  </oxd-form-row>
</template>

<script>
import Chip from '@ohrm/oxd/core/components/Chip/Chip.vue';
import {checkPassword, getPassLevel} from '@ohrm/core/util/helper/password';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';

export default {
  name: 'PasswordInput',
  components: {
    'oxd-chip': Chip,
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
  },
  emits: ['update:password', 'update:passwordConfirm'],
  data() {
    return {
      rules: {
        password: [required, shouldNotExceedCharLength(64), checkPassword],
        passwordConfirm: [
          required,
          shouldNotExceedCharLength(64),
          v => (!!v && v === this.password) || 'Passwords do not match',
        ],
      },
    };
  },

  computed: {
    passwordStrength() {
      let strength = 0;
      strength = getPassLevel(this.password).reduce((acc, val) => acc + val, 0);
      if (this.password.trim().length < 8) {
        strength = 0;
      }
      switch (strength) {
        case 2:
          return 'Weak';
        case 3:
          return 'Better';
        case 4:
          return 'Strongest';
        default:
          return 'Very Weak';
      }
    },
    chipClasses() {
      return {
        'user-password-chip': true,
        '--green': this.passwordStrength === 'Strongest',
      };
    },
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
    padding-top: 10px;
    padding-bottom: 10px;
    background-color: $oxd-background-white-shadow-color;
    border-radius: 0.75rem;
  }
  &-hint {
    font-size: 0.75rem;
  }
  &-cell {
    position: relative;
  }
  &-chip {
    font-family: $oxd-font-family;
    font-weight: 600;
    font-size: 0.75rem;
    position: absolute;
    right: 8px;
    top: 0px;
    &.--green {
      background-color: #93b40f;
    }
  }
}
</style>
