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
  <div class="orangehrm-background-container">
    <oxd-text
      tag="h4"
      class="orangehrm-welcome-screen-title orangehrm-welcome-screen-content"
      >System Check</oxd-text
    >
    <oxd-text class="orangehrm-welcome-screen-content"
      >In order for your orangeHRM installation to function properly,please
      ensure that all the system check items listed below are green.if any are
      red please take the necessary steps to fix them.</oxd-text
    >
  </div>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
export default {
  name: 'SystemCheckScreen',
  data() {
    return {
      selected: 'Orange',
    };
  },
  setup() {
    const http = new APIService('', '');
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
  }
}
</style>
