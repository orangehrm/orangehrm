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
  <oxd-text tag="p" class="orangehrm-form-hint">
    * {{ $t('general.required') }}
  </oxd-text>
</template>

<script>
export default {
  name: 'RequiredText',
};
</script>

<style lang="scss" scoped>
.orangehrm-form-hint {
  width: 100%;
  font-weight: 600;
  font-size: 0.75rem;
  margin-right: auto;
  @media screen and (min-width: 400px) {
    width: unset;
  }
}
</style>
