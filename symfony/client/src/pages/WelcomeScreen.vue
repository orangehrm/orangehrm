<template>
  <div class="orangehrm-background-container">
    <oxd-text tag="h4" class="orangehrm-welcome-screen-title"
      >Welcome to OrangeHRM Stater Version 5.0 Setup Wizard</oxd-text
    >
    <oxd-text
      >This setup wizard guide through the steps necessary to install/upgrade
      OrangeHRM Starter components and their dependencies</oxd-text
    >
    <oxd-text>Select an installation type</oxd-text>

    <oxd-radio-input
      id="check1"
      v-model="selected"
      value="install"
      option-label="Fresh Installation"
    />
    <oxd-text
      >Choose this option if you are installing OrangeHRM Starter for the first
      time</oxd-text
    >
    <oxd-radio-input
      id="check2"
      v-model="selected"
      value="upgrade"
      option-label="Upgrading an Existing Installation"
    />
    <oxd-text>Select an installation type</oxd-text>
    <oxd-text
      >Choose this option if you are already using a prior version of OrangeHRM
      Starter (version 2.6.5 to 4.8) and would like to upgrade to version
      5.0</oxd-text
    >
    <oxd-form-actions class="orangehrm-login-action">
      <oxd-button
        class="orangehrm-login-button"
        display-type="main"
        label="Next"
        type="submit"
      />
    </oxd-form-actions>
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
  &-title {
    color: $oxd-primary-one-color;
  }
}
</style>
