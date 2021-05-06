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
  <login-layout>
    <oxd-text class="orangehrm-login-title" tag="h5">Login</oxd-text>
    <div class="orangehrm-login-form">
      <div class="orangehrm-login-error">
        <!-- Login Errors Here -->
      </div>
      <oxd-form
        @submitValid="onSubmit"
        method="post"
        ref="loginForm"
        :action="submitUrl"
      >
        <oxd-form-row>
          <oxd-input-field
            name="username"
            label="Username"
            labelIcon="person"
            placeholder="username"
            v-model="username"
            :rules="rules.username"
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            name="password"
            label="Password"
            labelIcon="key"
            placeholder="password"
            v-model="password"
            type="password"
            :rules="rules.password"
          />
        </oxd-form-row>

        <oxd-form-actions class="orangehrm-login-action">
          <oxd-button
            class="orangehrm-login-button"
            displayType="main"
            label="Login"
            type="submit"
          />
        </oxd-form-actions>
      </oxd-form>
      <oxd-text class="orangehrm-login-pwreset" tag="p"
        >Forgot your <a href="#">Password?</a></oxd-text
      >
      <oxd-divider />
    </div>
  </login-layout>
</template>

<script>
import LoginLayout from '../components/LoginLayout';

import {urlFor} from '@orangehrm/core/util/helper/url';

export default {
  components: {
    'login-layout': LoginLayout,
  },
  data() {
    return {
      username: '',
      password: '',
      rules: {
        username: [v => (!!v && v.trim() !== '') || 'Required'],
        password: [v => (!!v && v.trim() !== '') || 'Required'],
      },
    };
  },

  computed: {
    submitUrl() {
      return urlFor('/auth/validate');
    },
  },

  methods: {
    onSubmit() {
      this.$refs.loginForm.$el.submit();
    },
  },
};
</script>

<style src="./login.scss" lang="scss" scoped></style>
