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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6">Login</oxd-text>

      <oxd-divider />

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
            v-model="username"
            :rules="rules.username"
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
            name="password"
            label="Password"
            v-model="password"
            type="password"
            :rules="rules.password"
          />
        </oxd-form-row>

        <oxd-divider />

        <oxd-button displayType="secondary" label="Login" type="submit" />
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {urlFor} from '@orangehrm/core/util/helper/url';

export default {
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

<style lang="scss" scoped>
.orangehrm-background-container {
  background-color: $oxd-background-light-gray-color;
  padding: 1.2rem;
}
</style>

<style lang="scss">
body {
  background-color: $oxd-background-light-gray-color;
}
</style>
