<template>
  <div class="orangehrm-login-social">
    <oxd-text class="orangehrm-login-social-text" tag="p"
      >Or login with</oxd-text
    >
    <div class="orangehrm-login-social-icons">
      <oxd-icon-button class="--loginwith" name="google"></oxd-icon-button>
      <oxd-icon-button class="--loginwith" name="facebook"></oxd-icon-button>
      <oxd-icon-button class="--loginwith" name="twitter"></oxd-icon-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'login-social',
};
</script>

<style src="./login-layout.scss" lang="scss" scoped></style>
