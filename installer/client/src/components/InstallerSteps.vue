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
  <ul class="orangehrm-installer-steps">
    <li
      v-for="(step, index) in steps"
      :key="step"
      :class="{
        'orangehrm-installer-steps-item': true,
        '--active': currentStep === index,
      }"
    >
      <oxd-text
        tag="span"
        :class="{
          'orangehrm-installer-steps-count': true,
          '--success': currentStep > index,
        }"
      >
        <template v-if="currentStep <= index">{{ index + 1 }}</template>
        <oxd-icon v-else class="orangehrm-installer-steps-icon" name="check" />
      </oxd-text>

      <oxd-text tag="span" class="orangehrm-installer-steps-name">
        {{ step }}
      </oxd-text>
    </li>
  </ul>
</template>

<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon.vue';

export default {
  name: 'InstallerSteps',
  components: {
    'oxd-icon': Icon,
  },
  props: {
    steps: {
      type: Array,
      default: () => [],
    },
    currentStep: {
      type: Number,
      default: 0,
    },
  },
};
</script>

<style src="./installer-steps.scss" lang="scss" scoped></style>
