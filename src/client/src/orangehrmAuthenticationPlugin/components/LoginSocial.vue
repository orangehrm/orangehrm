<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-login-social">
    <oxd-text class="orangehrm-login-social-text" tag="p">
      {{ $t('general.or_login_with') }}
    </oxd-text>
    <div class="orangehrm-login-social-icons">
      <oxd-icon-button class="--loginwith" name="google"></oxd-icon-button>
      <oxd-icon-button class="--loginwith" name="facebook"></oxd-icon-button>
      <oxd-icon-button class="--loginwith" name="twitter"></oxd-icon-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoginSocial',
};
</script>

<style src="./login-layout.scss" lang="scss" scoped></style>
