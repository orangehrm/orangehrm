<template>
  <div class="orangehrm-forgot-password-container">
    <div class="orangehrm-forgot-password-wrapper">
      <div class="orangehrm-card-container">
        <oxd-text tag="h6" class="orangehrm-forgot-password-title">
          Reset password
        </oxd-text>
        <oxd-divider />
        <card-note
          note-text="The user account cannot be found or the reset password token has expired"
          class="orangehrm-forgot-password-card-note"
          note-icon
        />
        <oxd-text tag="p">
          <oxd-text tag="span" class="orangehrm-forgot-password-reset">
            Click here
          </oxd-text>
          to request for a new password reset token
        </oxd-text>
      </div>
    </div>
    <slot name="footer"></slot>
  </div>
</template>

<script>
import CardNote from '../components/CardNote';
export default {
  name: 'ResetPasswordError',
  components: {
    'card-note': CardNote,
  },
};
</script>
<style src="./reset-password.scss" lang="scss" scoped></style>
