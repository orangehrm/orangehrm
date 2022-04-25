<template>
  <div class="orangehrm-installer-page">
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Upgrade Complete
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      You have successfully upgraded to OrangeHRM Starter version 5.0. Make sure
      you go through the following steps before start using the system.
    </oxd-text>

    <ul class="orangehrm-upgrader-complete">
      <li>
        <oxd-text tag="span" class="orangehrm-installer-page-content">
          If notes were displayed under <b>Version Info,</b> make sure you
          understand them thoroughly, You can find the notes at
          <b>src/log/installer.log</b> in your OrangeHRM root folder.
        </oxd-text>
      </li>
      <li>
        <oxd-text tag="span" class="orangehrm-installer-page-content">
          After going through the manual steps mentioned in the notes (if there
          are any), do a full round of data verification to see whether data
          from previous version has been imported properly.
        </oxd-text>
      </li>
    </ul>

    <oxd-button
      class="orangehrm-upgrader-complete-button"
      display-type="secondary"
      label="Launch OrangeHRM"
      @click="launch"
    />
  </div>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation.ts';
export default {
  name: 'UpgraderCompleteScreen',
  methods: {
    launch() {
      navigate('/');
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.orangehrm-upgrader-complete {
  padding: 0 1rem;
  color: $oxd-interface-gray-darken-1-color;
  li {
    padding-bottom: 0.75rem;
  }
}
.orangehrm-upgrader-complete-button {
  margin-right: auto;
}
</style>
