<template>
  <div class="orangehrm-upgrader-complete orangehrm-upgrader-container">
    <oxd-text
      tag="h5"
      class="orangehrm-upgrader-complete-title orangehrm-upgrader-container-content"
    >
      Upgrade Complete
    </oxd-text>
    <oxd-text
      class="orangehrm-upgrader-complete-content orangehrm-upgrader-container-content"
    >
      You have sucessfully upgraded to OrangeHRM Starter version 5.0. Make sure
      you go through the following steps before start using the system.
    </oxd-text>

    <div class="orangehrm-upgrader-complete-row">
      <ul class="orangehrm-upgrader-complete-lists">
        <li>
          If note were displayed under <b>Version Info,</b> make sure you
          understand them throughly, You can find the notes at
          <b>Upgrading/log/notes.log</b> in your OrangeHRM root folder.
        </li>
        <li>
          After going through the manual steps mentioned in the notes (if there
          are any), do a full round of data verification to see whether data
          from previous version has been imported properly.
        </li>
      </ul>
    </div>

    <oxd-form-actions
      class="orangehrm-upgrader-complete-action orangehrm-upgrader-container-action"
    >
      <oxd-button
        display-type="secondary"
        label="Launch OrangeHRM"
        type="submit"
      />
    </oxd-form-actions>
  </div>
</template>

<script>
export default {
  name: 'UpgraderCompleteScreen',
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style scoped lang="scss">
.orangehrm-upgrader-complete {
  &-title {
    padding-top: 0;
    color: $oxd-primary-one-color;
  }
  &-lists {
    padding: 0 1.5rem;
    color: $oxd-interface-gray-darken-1-color;
    li {
      padding-bottom: 0.75rem;
    }
  }
  &-action {
    padding-right: 0;
    justify-content: flex-start;
    button {
      margin-right: 0.5rem;
    }
  }
}
</style>
