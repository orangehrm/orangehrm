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

      <oxd-form @submitValid="onSubmit($event)" method="post">
        <oxd-form-row>
          <oxd-input-field
            label="Username"
            v-model="username"
            :rules="rules.username"
          />
        </oxd-form-row>

        <oxd-form-row>
          <oxd-input-field
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

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  data() {
    return {
      username: '',
      password: '',
      rules: {
        username: [(v: string) => (!!v && v.trim() !== '') || 'Required'],
        password: [(v: string) => (!!v && v.trim() !== '') || 'Required'],
      },
    };
  },

  methods: {
    onSubmit() {
      this.$http
        .post(`/api/v1/auth/login`, {
          username: this.username,
          password: this.password,
        })
        .then(() => {
          console.log('success');
        })
        .catch((error: Error) => {
          console.log(error);
        });
    },
  },
});
</script>

<style lang="scss" scoped>
.orangehrm-background-container {
  background-color: $oxd-background-light-gray-color;
  padding: 1.2rem;
}
</style>
