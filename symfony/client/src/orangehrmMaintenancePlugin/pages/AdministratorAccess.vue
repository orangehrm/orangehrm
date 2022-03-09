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
  <div>
    <oxd-form
      ref="verifyForm"
      method="post"
      :action="submitUrl"
      @submitValid="onSubmit"
    >
      <!--      <input name="_token" :value="token" type="hidden" />-->
      <oxd-form-row>
        <oxd-input-field
          :model-value="username"
          name="username"
          label="Username"
          readonly
        />
      </oxd-form-row>
      <oxd-form-row>
        <oxd-input-field
          v-model="password"
          name="password"
          label="Password"
          type="password"
        />
      </oxd-form-row>
      <oxd-form-actions>
        <oxd-button
          class="orangehrm-login-button"
          display-type="main"
          label="Login"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
import {urlFor} from '@ohrm/core/util/helper/url';

export default {
  name: 'AdministratorAccess',

  props: {
    username: {
      type: String,
      required: true,
    },
    //   token: {
    //     type: String,
    //     required: true,
    //   },
  },

  data() {
    return {
      password: '',
    };
  },

  computed: {
    submitUrl() {
      return urlFor('/auth/verify');
    },
  },

  methods: {
    onSubmit() {
      this.$refs.verifyForm.$el.submit();
    },
  },
};
</script>

<style scoped></style>
