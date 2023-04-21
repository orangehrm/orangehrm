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
  <div class="orangehrm-installer">
    <div class="orangehrm-installer-sidebar">
      <installer-steps
        :steps="steps"
        :current-step="currentStep"
      ></installer-steps>
      <div class="orangehrm-installer-footer">
        <a
          v-show="currentStep === 0"
          class="orangehrm-installer-guide"
          @click="goToLoginPage"
        >
          Login
        </a>
        <br />
        <slot name="footer"></slot>
      </div>
    </div>
    <div class="orangehrm-installer-header">
      <img :src="brandingSrc" width="280" alt="orangehrm-branding" />
    </div>
    <div class="orangehrm-installer-content">
      <slot></slot>
    </div>
  </div>
</template>

<script>
import InstallerSteps from '@/components/InstallerSteps.vue';

export default {
  name: 'InstallerLayout',
  components: {
    'installer-steps': InstallerSteps,
  },
  props: {
    steps: {
      type: Array,
      required: true,
    },
    currentStep: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      brandingSrc: `${window.appGlobal.baseUrl}/../../web/images/ohrm_branding.png`,
    };
  },

  methods: {
    goToLoginPage() {
      window.location.href = window.location.origin;
    },
  },
};
</script>

<style src="./installer-layout.scss" lang="scss" scoped></style>
