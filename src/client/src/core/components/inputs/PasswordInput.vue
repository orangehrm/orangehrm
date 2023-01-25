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
          :label="passwordStrengthLabel"
        />
        <oxd-input-field
          type="password"
          autocomplete="off"
          :required="true"
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
          :required="true"
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
import {APIService} from '@/core/util/services/api.service';
import {OxdChip, promiseDebounce} from '@eth0/oxd-experimental';

export default {
  name: 'PasswordInput',
  components: {
    'oxd-chip': OxdChip,
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
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '');
    return {
      http,
    };
  },
  data() {
    return {
      passwordStrength: 0,
      rules: {
        password: [
          required,
          shouldNotExceedCharLength(64),
          promiseDebounce(this.checkPassword, 500),
        ],
        passwordConfirm: [
          required,
          shouldNotExceedCharLength(64),
          (v) =>
            (!!v && v === this.password) ||
            this.$t('general.passwords_do_not_match'),
        ],
      },
    };
  },

  computed: {
    passwordStrengthLabel() {
      switch (this.passwordStrength) {
        case 1:
          return this.$t('general.weak');
        case 2:
          return this.$t('general.better');
        case 3:
          return this.$t('general.strong');
        case 4:
          return this.$t('general.strongest');
        default:
          return this.$t('general.very_weak');
      }
    },
    chipClasses() {
      return {
        'user-password-chip': true,
        '--green': this.passwordStrength === 4,
        '--lightGreen': this.passwordStrength === 3,
        '--yellow': this.passwordStrength === 2,
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

  methods: {
    checkPassword(password) {
      return new Promise((resolve) => {
        if (password.trim() !== '') {
          this.http
            .request({
              method: 'POST',
              url: `api/v2/auth/public/validation/password`,
              data: {
                password,
              },
            })
            .then((response) => {
              const {data, meta} = response.data;
              this.passwordStrength = meta?.strength || 0;
              if (Array.isArray(data?.messages) && data.messages.length > 0) {
                resolve(data.messages[0]);
              } else {
                resolve(true);
              }
            });
        } else {
          this.passwordStrength = 0;
          resolve(true);
        }
      });
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
    top: -5px;
    &.--green {
      background-color: #93b40f;
    }
    &.--lightGreen {
      background-color: #bde813;
    }
    &.--yellow {
      background-color: #fcff00;
    }
  }
}
</style>
