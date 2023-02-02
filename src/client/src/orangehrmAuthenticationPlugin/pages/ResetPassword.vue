<!--
  - OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  - all the essential functionalities required for any enterprise.
  - Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
  -
  - OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  - the GNU General Public License as published by the Free Software Foundation; either
  - version 2 of the License, or (at your option) any later version.
  -
  - OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  - without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  - See the GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License along with this program;
  - if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  - Boston, MA  02110-1301, USA
  -->

<template>
  <div class="orangehrm-forgot-password-container">
    <div class="orangehrm-forgot-password-wrapper">
      <div class="orangehrm-card-container">
        <oxd-form
          ref="resetForm"
          method="post"
          :action="submitUrl"
          @submit-valid="onSubmit"
        >
          <oxd-text tag="h6">
            {{ $t('auth.reset_password') }}
          </oxd-text>
          <oxd-divider />
          <card-note
            :note-text="$t('auth.set_new_password')"
            class="orangehrm-forgot-password-card-note"
          />
          <input name="_token" :value="token" type="hidden" />
          <oxd-form-row>
            <oxd-input-field
              :value="username"
              name="username"
              :label="$t('auth.username')"
              label-icon="person"
              readonly
            />
          </oxd-form-row>
          <oxd-form-row class="orangehrm-forgot-password-row">
            <oxd-chip
              v-if="user.newPassword"
              :class="chipClasses"
              :label="passwordStrengthLabel"
            />
            <oxd-input-field
              v-model="user.newPassword"
              name="password"
              :label="$t('auth.new_password')"
              label-icon="key"
              :placeholder="$t('auth.password')"
              type="password"
              :rules="rules.newPassword"
              autocomplete="off"
            />
          </oxd-form-row>
          <oxd-form-row>
            <oxd-input-field
              v-model="user.confirmPassword"
              name="confirmPassword"
              :label="$t('general.confirm_password')"
              label-icon="key"
              :placeholder="$t('auth.password')"
              type="password"
              :rules="rules.confirmPassword"
              autocomplete="off"
            />
          </oxd-form-row>
          <oxd-divider />
          <div class="orangehrm-forgot-password-buttons">
            <oxd-button
              class="orangehrm-forgot-password-button"
              display-type="secondary"
              size="large"
              :label="$t('auth.reset_password')"
              type="submit"
            />
          </div>
        </oxd-form>
      </div>
    </div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import CardNote from '../components/CardNote';
import {
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {urlFor} from '@/core/util/helper/url';
import {OxdChip, promiseDebounce} from '@ohrm/oxd';
import {APIService} from '@/core/util/services/api.service';

export default {
  name: 'ResetPassword',
  components: {
    'oxd-chip': OxdChip,
    'card-note': CardNote,
  },
  props: {
    username: {
      type: String,
      required: true,
    },
    token: {
      type: String,
      required: true,
    },
  },
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '');
    return {
      http,
    };
  },
  data() {
    return {
      passwordStrength: 0,
      user: {
        username: '',
        newPassword: '',
        confirmPassword: '',
      },
      rules: {
        newPassword: [
          required,
          shouldNotExceedCharLength(64),
          promiseDebounce(this.checkPassword, 500),
        ],
        confirmPassword: [
          required,
          shouldNotExceedCharLength(64),
          (v) =>
            (!!v && v === this.user.newPassword) ||
            this.$t('general.passwords_do_not_match'),
        ],
      },
    };
  },
  computed: {
    submitUrl() {
      return urlFor('/auth/resetPassword');
    },
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
        'orangehrm-forgot-password-chip': true,
        '--green': this.passwordStrength === 4,
        '--lightGreen': this.passwordStrength === 3,
        '--yellow': this.passwordStrength === 2,
      };
    },
  },
  methods: {
    onSubmit() {
      this.$refs.resetForm.$el.submit();
    },
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

<style src="./reset-password.scss" lang="scss" scoped></style>
