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
  <div class="orangehrm-background-container orangehrm-welcome-screen ">
    <oxd-text
      tag="h4"
      class="orangehrm-welcome-screen-title orangehrm-welcome-screen-content"
      >Welcome to OrangeHRM Stater Version 5.0 Setup Wizard</oxd-text
    >
    <oxd-text class="orangehrm-welcome-screen-content"
      >This setup wizard guide through the steps necessary to install/upgrade
      OrangeHRM Starter components and their dependencies</oxd-text
    >
    <oxd-text class="orangehrm-welcome-screen-content"
      >Select an installation type</oxd-text
    >
    <oxd-form ref="welcomeForm" method="post">
      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-radio-input
              id="install"
              v-model="selected"
              value="install"
              option-label="Fresh Installation"
            />
            <oxd-text
              class="orangehrm-welcome-screen-content orangehrm-welcome-screen-content--note"
              >Choose this option if you are installing OrangeHRM Starter for
              the first time</oxd-text
            >
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-row>
        <oxd-grid :cols="1" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-radio-input
              id="check2"
              v-model="selected"
              value="upgrade"
              option-label="Upgrading an Existing Installation"
            />
            <oxd-text
              class="orangehrm-welcome-screen-content orangehrm-welcome-screen-content--note"
              >Choose this option if you are already using a prior version of
              OrangeHRM Starter (version 2.6.5 to 4.8) and would like to upgrade
              to version 5.0</oxd-text
            >
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-text class="orangehrm-welcome-screen-content"
        >Click
        <oxd-text class="orangehrm-welcome-screen-content-bold" tag="span"
          >Next</oxd-text
        >
        to continue</oxd-text
      >
      <oxd-form-actions class="orangehrm-welcome-screen-action">
        <oxd-button
          class="orangehrm-welcome-screen-button"
          display-type="secondary"
          label="Next"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </div>
</template>

<script>
export default {
  name: 'WelcomeScreen',
  data() {
    return {
      selected: 'Orange',
    };
  },
};
</script>

<style scoped lang="scss">
.orangehrm-welcome-screen {
  &-action {
    padding: 1rem;
  }
  &-title {
    color: $oxd-primary-one-color;
  }
  &-content {
    padding: 0.75rem;
    &-bold {
      font-weight: 700;
    }
    &--note {
      padding-top: 0;
      padding-left: 2rem;
    }
  }
}
.orangehrm-welcome-screen {
  ::v-deep(.oxd-radio-wrapper label) {
    font-size: 16px;
    font-weight: 700;
  }
}
</style>
