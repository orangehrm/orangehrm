<template>
  <div class="orangehrm-installer-page">
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Upgrade Complete
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      You have successfully upgraded to OrangeHRM Starter version
      {{ productversion }}.
    </oxd-text>
    <br />
    <br />
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
  props: {
    productversion: {
      type: String,
      required: true,
    },
  },
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
}
.orangehrm-upgrader-complete-button {
  margin-right: auto;
}
</style>
