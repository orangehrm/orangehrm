<template>
  <div class="orangehrm-login-layout">
    <div class="orangehrm-login-layout-blob">
      <div class="orangehrm-login-container">
        <div class="orangehrm-login-slot-wrapper">
          <login-branding :imgSrc="brandingSrc"></login-branding>
          <div class="orangehrm-login-slot">
            <div class="orangehrm-login-logo-mobile">
              <img :src="logoSrc" alt="orangehrm-logo" />
            </div>
            <slot></slot>
            <login-social></login-social>
          </div>
        </div>
      </div>
      <div class="orangehrm-login-logo">
        <img :src="logoSrc" alt="orangehrm-logo" />
      </div>
    </div>
  </div>
</template>

<script>
import LoginBranding from './LoginBranding';
import LoginSocial from './LoginSocial';

export default {
  name: 'login-layout',
  components: {
    'login-branding': LoginBranding,
    'login-social': LoginSocial,
  },
  data() {
    return {
      logoSrc: `${window.appGlobal.baseUrl}/../images/ohrm_logo.png`,
      brandingSrc: `${window.appGlobal.baseUrl}/../images/ohrm_branding.png`,
    };
  },
};
</script>

<style src="./login-layout.scss" lang="scss" scoped></style>
