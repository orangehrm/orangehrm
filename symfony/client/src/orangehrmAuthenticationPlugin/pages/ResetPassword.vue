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
        <oxd-form method="post">
          <oxd-text tag="h6" class="orangehrm-forgot-password-title">
            Reset password
          </oxd-text>
          <oxd-divider />
          <card-note
            note-text="Set a new Password"
            class="orangehrm-forgot-password-card-note"
          />
          <oxd-form-row>
            <oxd-input-field
              v-model="user.username"
              name="username"
              label="Username"
              label-icon="person"
              :rules="rules.username"
              placeholder="username"
              autocomplete="off"
              required
            />
          </oxd-form-row>
          <oxd-form-row>
            <oxd-input-field
              v-model="user.newPassword"
              name="password"
              label="New Password"
              label-icon="key"
              placeholder="password"
              type="password"
              :rules="rules.newPassword"
              autocomplete="off"
              required
            />
          </oxd-form-row>
          <oxd-form-row>
            <oxd-input-field
              v-model="user.confirmPassword"
              name="username"
              label="Confirm Password"
              label-icon="key"
              placeholder="password"
              type="password"
              :rules="rules.confirmPassword"
              autocomplete="off"
              required
            />
          </oxd-form-row>
          <oxd-divider />
          <div class="orangehrm-forgot-password-button-container">
            <oxd-button
              class="orangehrm-forgot-password-button"
              display-type="secondary"
              size="large"
              label="Reset Password"
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
import {required} from '@/core/util/validation/rules';
import CardNote from '../components/CardNote';

export default {
  name: 'ResetPassword',
  components: {
    'card-note': CardNote,
  },
  data() {
    return {
      user: {
        username: '',
        newPassword: '',
        confirmPassword: '',
      },
      rules: {
        username: [required],
        newPassword: [required],
        confirmPassword: [required],
      },
    };
  },
};
</script>

<style src="./reset-password.scss" lang="scss" scoped></style>
