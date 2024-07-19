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
  <div class="orangehrm-upgrade-container">
    <a
      class="orangehrm-upgrade-link"
      target="_blank"
      href="https://orangehrm.com/open-source/upgrade-to-advanced"
    >
      <oxd-glass-button
        :label="$t('general.upgrade')"
        size="large"
        class="orangehrm-upgrade-button"
      >
        <template #icon>
          <oxd-icon
            type="svg"
            name="upgrade"
            class="orangehrm-upgrade-icon"
          ></oxd-icon>
        </template>
      </oxd-glass-button>
    </a>
  </div>
</template>

<script>
import {OxdGlassButton, OxdIcon} from '@ohrm/oxd';

export default {
  name: 'UpgradeButton',
  components: {
    'oxd-icon': OxdIcon,
    'oxd-glass-button': OxdGlassButton,
  },
};
</script>

<style src="./upgrade-button.scss" scoped lang="scss"></style>
