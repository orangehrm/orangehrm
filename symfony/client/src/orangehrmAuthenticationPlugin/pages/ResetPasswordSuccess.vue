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
        <oxd-text tag="h6" class="orangehrm-forgot-password-title">
          Reset password link successfully
        </oxd-text>
        <oxd-divider />
        <card-note
          note-text="A reset password link has been sent to you via email"
          class="orangehrm-forgot-password-card-note"
        />
        <card-note
          note-text="You can follow that link and select a new password"
          class="orangehrm-forgot-password-card-note"
        />
        <card-note
          note-text="If the email does not arrive,please contact your OrangeHRM Adminstrator"
          class="orangehrm-forgot-password-card-note"
          note
        />
      </div>
    </div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import CardNote from '../components/CardNote';

export default {
  name: 'ResetPasswordSuccess',
  components: {
    'card-note': CardNote,
  },
};
</script>

<style src="./reset-password.scss" lang="scss" scoped></style>
