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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <div class="orangehrm-module-under-development">
        <oxd-text tag="h4" class="orangehrm-module-under-development-heading">
          {{ $t('general.launching_soon') }}
        </oxd-text>
        <img
          :src="moduleUnderDevelopment"
          class="orangehrm-module-under-development-img"
          alt="this module is under development"
        />
        <oxd-text tag="p" class="orangehrm-module-under-development-text">
          {{ $t('general.this_page_is_being_developed') }}
          <a
            target="_blank"
            class="orangehrm-module-under-development-link"
            href="https://sourceforge.net/projects/orangehrm/files/latest/download"
          >
            {{ $t('general.click_here') }}
          </a>
          {{ $t('general.download_latest_release_with_all_features') }}
        </oxd-text>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      moduleUnderDevelopment: `${window.appGlobal.baseUrl}/../images/web_under_construction.png`,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-module-under-development {
  text-align: center;
  &-img {
    max-width: 100%;
    height: 100%;
    max-height: 350px;
    display: block;
    margin: 0 auto;
    object-fit: fill;
  }
  &-heading {
    color: $oxd-primary-one-color;
    font-weight: 700;
  }
  &-text {
    font-size: 14px;
    max-width: 800px;
    margin: 0 auto;
    margin-bottom: 2rem;
  }
  &-link {
    text-decoration: none;
    color: $oxd-primary-one-color;
  }
}
</style>
