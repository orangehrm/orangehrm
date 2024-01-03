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
  <oxd-chip :class="chipClasses" :label="passwordStrengthLabel" />
</template>

<script>
import {OxdChip} from '@ohrm/oxd';

export default {
  name: 'PasswordStrengthIndicator',

  components: {
    'oxd-chip': OxdChip,
  },

  props: {
    passwordStrength: {
      type: Number,
      required: true,
    },
  },

  computed: {
    passwordStrengthLabel() {
      switch (this.passwordStrength) {
        case 1:
          return this.$t('general.weak');
        case 2:
          return this.$t('general.better');
        case 3:
          return this.$t('general.strong');
        case 4:
          return this.$t('general.strongest');
        default:
          return this.$t('general.very_weak');
      }
    },
    chipClasses() {
      return {
        'orangehrm-password-chip': true,
        '--strength-better': this.passwordStrength === 2,
        '--strength-strong': this.passwordStrength === 3,
        '--strength-strongest': this.passwordStrength === 4,
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-password {
  &-chip {
    top: 0px;
    right: 0px;
    font-weight: 600;
    position: absolute;
    color: $oxd-white-color;
    font-family: $oxd-font-family;

    &.--strength-better {
      color: #979900;
      background-color: #fcff00;
    }
    &.--strength-strong {
      background-color: #bde813;
    }
    &.--strength-strongest {
      background-color: #93b40f;
    }
  }
}
</style>
