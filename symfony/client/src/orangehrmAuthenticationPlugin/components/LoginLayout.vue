<template>
  <div class="orangehrm-login-layout">
    <div class="orangehrm-login-layout-blob">
      <div class="orangehrm-login-container">
        <div class="orangehrm-login-slot">
          <slot></slot>
        </div>
      </div>
      <div class="orangehrm-login-logo">
        <img :src="logoSrc" alt="orangehrm-logo" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'login-layout',
  data() {
    return {
      logoSrc: `${window.appGlobal.baseUrl}/../images/ohrm_logo.png`,
    };
  },
};
</script>

<style src="./login-layout.scss" lang="scss" scoped></style>
