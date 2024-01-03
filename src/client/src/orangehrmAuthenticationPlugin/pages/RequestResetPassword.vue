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
        <oxd-form
          ref="resetForm"
          method="post"
          :action="submitUrl"
          @submit-valid="onSubmit"
        >
          <oxd-text tag="h6" class="orangehrm-forgot-password-title">
            {{ $t('auth.reset_password') }}
          </oxd-text>
          <oxd-divider />
          <card-note
            :note-text="$t('auth.username_identify_reset_note')"
            class="orangehrm-forgot-password-card-note"
          />
          <input name="_token" :value="token" type="hidden" />
          <oxd-form-row>
            <oxd-input-field
              v-model="username"
              name="username"
              :label="$t('auth.username')"
              label-icon="person"
              :rules="rules.username"
              :placeholder="$t('auth.username')"
            />
          </oxd-form-row>
          <oxd-divider />
          <div class="orangehrm-forgot-password-button-container">
            <oxd-button
              class="orangehrm-forgot-password-button orangehrm-forgot-password-button--cancel"
              display-type="ghost"
              size="large"
              :label="$t('general.cancel')"
              @click="onCancel"
            />
            <oxd-button
              class="orangehrm-forgot-password-button orangehrm-forgot-password-button--reset"
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
import {navigate} from '@/core/util/helper/navigation';
import {required} from '@/core/util/validation/rules';
import CardNote from '../components/CardNote';
import {urlFor} from '@/core/util/helper/url';

export default {
  name: 'RequestResetPassword',
  components: {
    'card-note': CardNote,
  },
  props: {
    token: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      username: '',
      rules: {
        username: [required],
      },
    };
  },
  computed: {
    submitUrl() {
      return urlFor('/auth/requestResetPassword');
    },
  },
  methods: {
    onCancel() {
      navigate('/auth/login');
    },
    onSubmit() {
      this.$refs.resetForm.$el.submit();
    },
  },
};
</script>

<style src="./reset-password.scss" lang="scss" scoped></style>
