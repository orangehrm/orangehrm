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
    <template v-if="!baseUrlStored">
      <br />
      <oxd-text tag="p" class="orangehrm-installer-page-content">
        No application URL is stored, so password reset emails are not sent. Set
        it by running <b>php bin/console orangehrm:set-base-url &lt;url&gt;</b>
        from the OrangeHRM directory, where &lt;url&gt; is the OrangeHRM address
        as shown in the browser before /auth/login, e.g.
        https://hr.example.com/orangehrm/index.php.
      </oxd-text>
    </template>
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
    baseUrlStored: {
      type: Boolean,
      default: true,
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
