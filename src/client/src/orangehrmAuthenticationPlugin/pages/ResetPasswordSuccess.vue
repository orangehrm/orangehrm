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
  <div class="orangehrm-forgot-password-container">
    <div class="orangehrm-forgot-password-wrapper">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-forgot-password-title">
          {{ $t('auth.reset_password_link_sent_successfully') }}
        </oxd-text>
        <oxd-divider />
        <card-note
          :note-text="$t('auth.reset_password_link_via_email')"
          class="orangehrm-forgot-password-card-note"
        />
        <card-note
          :note-text="$t('auth.follow_link_to_select_password')"
          class="orangehrm-forgot-password-card-note"
        />
        <card-note
          :note-text="$t('auth.email_not_receive_note')"
          class="orangehrm-forgot-password-card-note"
          has-note
        />
        <oxd-divider />
        <oxd-form @submit-valid="onSubmitResetCode">
          <oxd-text tag="h6" class="orangehrm-forgot-password-title">
            {{ $t('auth.received_reset_code_instead_of_link') }}
          </oxd-text>
          <card-note
            :note-text="$t('auth.enter_reset_code_from_email')"
            class="orangehrm-forgot-password-card-note"
          />
          <oxd-form-row>
            <oxd-input-field
              v-model="resetCode"
              :label="$t('auth.reset_code')"
              :rules="rules.resetCode"
            />
          </oxd-form-row>
          <div class="orangehrm-forgot-password-button-container">
            <oxd-button
              class="orangehrm-forgot-password-button orangehrm-forgot-password-button--reset"
              display-type="secondary"
              size="large"
              :label="$t('auth.continue')"
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
import {navigate} from '@/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';
import CardNote from '../components/CardNote';

export default {
  name: 'ResetPasswordSuccess',
  components: {
    'card-note': CardNote,
  },
  data() {
    return {
      resetCode: '',
      rules: {
        resetCode: [
          required,
          // The reset code is base64url, as in the link the email would otherwise contain
          (value) =>
            /^[A-Za-z0-9_-]+$/.test(value.trim()) || this.$t('general.invalid'),
        ],
      },
    };
  },
  methods: {
    onSubmitResetCode() {
      navigate('/auth/resetPassword/resetCode/{resetCode}', {
        resetCode: this.resetCode.trim(),
      });
    },
  },
};
</script>

<style src="./reset-password.scss" lang="scss" scoped></style>
