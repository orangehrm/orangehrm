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
  <login-layout>
    <oxd-text class="orangehrm-login-title" tag="h5">
      {{ $t('auth.login') }}
    </oxd-text>
    <div class="orangehrm-login-form">
      <div class="orangehrm-login-error">
        <oxd-alert
          :show="error !== null"
          :message="error?.message || ''"
          type="error"
        ></oxd-alert>
        <oxd-sheet
          v-if="isDemoMode"
          type="gray-lighten-2"
          class="orangehrm-demo-credentials"
        >
          <oxd-text tag="p">Username : Admin</oxd-text>
          <oxd-text tag="p">Password : admin123</oxd-text>
        </oxd-sheet>
      </div>
      <oxd-form
        ref="loginForm"
        method="post"
        :action="submitUrl"
        @submit-valid="onSubmit"
      >
        <input name="_token" :value="token" type="hidden" />

        <oxd-form-row>
          <oxd-input-field
            v-model="username"
            name="username"
            :label="$t('general.username')"
            label-icon="person"
            :placeholder="$t('auth.username')"
            :rules="rules.username"
            autofocus
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            v-model="password"
            name="password"
            :label="$t('general.password')"
            label-icon="key"
            :placeholder="$t('auth.password')"
            type="password"
            :rules="rules.password"
          />
        </oxd-form-row>

        <oxd-form-actions class="orangehrm-login-action">
          <oxd-button
            class="orangehrm-login-button"
            display-type="main"
            :label="$t('auth.login')"
            type="submit"
          />
        </oxd-form-actions>
        <div class="orangehrm-login-forgot">
          <oxd-text class="orangehrm-login-forgot-header" @click="navigateUrl">
            {{ $t('auth.forgot_password') }}?
          </oxd-text>
        </div>
      </oxd-form>
      <template v-if="authenticators.length > 0">
        <oxd-divider class="orangehrm-login-seperator"></oxd-divider>
        <social-media-auth :authenticators="authenticators"></social-media-auth>
      </template>
    </div>
    <div class="orangehrm-login-footer">
      <div v-if="showSocialMedia" class="orangehrm-login-footer-sm">
        <a
          href="https://www.linkedin.com/company/orangehrm/mycompany/"
          target="_blank"
        >
          <oxd-icon type="svg" class="orangehrm-sm-icon" name="linkedinFill" />
        </a>
        <a href="https://www.facebook.com/OrangeHRM/" target="_blank">
          <oxd-icon type="svg" class="orangehrm-sm-icon" name="facebookFill" />
        </a>
        <a href="https://twitter.com/orangehrm?lang=en" target="_blank">
          <oxd-icon type="svg" class="orangehrm-sm-icon" name="twitterFill" />
        </a>
        <a href="https://www.youtube.com/c/OrangeHRMInc" target="_blank">
          <oxd-icon type="svg" class="orangehrm-sm-icon" name="youtubeFill" />
        </a>
      </div>
      <slot name="footer"></slot>
    </div>
  </login-layout>
</template>

<script>
import {urlFor} from '@ohrm/core/util/helper/url';
import {OxdAlert, OxdIcon, OxdSheet} from '@ohrm/oxd';
import {required} from '@ohrm/core/util/validation/rules';
import {navigate, reloadPage} from '@ohrm/core/util/helper/navigation';
import LoginLayout from '@/orangehrmAuthenticationPlugin/components/LoginLayout.vue';
import SocialMediaAuth from '@/orangehrmAuthenticationPlugin/components/SocialMediaAuth.vue';

export default {
  components: {
    'oxd-icon': OxdIcon,
    'oxd-alert': OxdAlert,
    'oxd-sheet': OxdSheet,
    'login-layout': LoginLayout,
    'social-media-auth': SocialMediaAuth,
  },

  props: {
    error: {
      type: Object,
      default: () => null,
    },
    token: {
      type: String,
      required: true,
    },
    showSocialMedia: {
      type: Boolean,
      default: true,
    },
    isDemoMode: {
      type: Boolean,
      default: false,
    },
    authenticators: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      username: '',
      password: '',
      rules: {
        username: [required],
        password: [required],
      },
      submitted: false,
    };
  },

  computed: {
    submitUrl() {
      return urlFor('/auth/validate');
    },
  },

  beforeMount() {
    setTimeout(() => {
      reloadPage();
    }, 1200000); // 20 * 60 * 1000 (20 minutes);
  },

  methods: {
    onSubmit() {
      if (!this.submitted) {
        this.submitted = true;
        this.$refs.loginForm.$el.submit();
      }
    },
    navigateUrl() {
      navigate('/auth/requestPasswordResetCode');
    },
  },
};
</script>

<style src="./login.scss" lang="scss" scoped></style>
