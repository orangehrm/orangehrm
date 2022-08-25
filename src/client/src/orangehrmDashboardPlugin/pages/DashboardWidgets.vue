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
  <div class="orangehrm-dashboard-container">
    <oxd-grid :cols="3">
      <oxd-grid-item>
        <base-widget
          icon="house-door-fill"
          title="test widget 1"
          empty-content-text="No Test Contetnt Here"
        >
          <template #action>
            <action-button icon="gear-fill"></action-button>
          </template>
        </base-widget>
      </oxd-grid-item>
    </oxd-grid>
  </div>
</template>
<script>
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';
import ActionButton from '@/orangehrmDashboardPlugin/components/ActionButton';

export default {
  name: 'Dashboard',
  components: {
    'base-widget': BaseWidget,
    'action-button': ActionButton,
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-dashboard-container {
  box-sizing: border-box;
}
::v-deep(.oxd-grid-3) {
  width: max-content;
  margin-left: auto;
  margin-right: auto;
}
</style>
