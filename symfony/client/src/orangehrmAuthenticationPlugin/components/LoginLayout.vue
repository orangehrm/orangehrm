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
  <div class="orangehrm-login-layout">
    <div class="orangehrm-login-layout-blob">
      <div class="orangehrm-login-container">
        <div class="orangehrm-login-slot-wrapper">
          <login-branding :img-src="brandingSrc"></login-branding>
          <div class="orangehrm-login-slot">
            <div class="orangehrm-login-logo-mobile">
              <img :src="logoSrc" alt="orangehrm-logo" />
            </div>
            <slot></slot>
            <slot name="footer"></slot>
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

export default {
  name: 'LoginLayout',
  components: {
    'login-branding': LoginBranding,
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
