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
  <oxd-sheet :gutters="false" class="orangehrm-dashboard-widget">
    <div v-show="showNoContent">
      <div></div>
      <oxd-text>
        {{ noContentText }}
      </oxd-text>
    </div>
    <div >
      <oxd-icon :name="iconName"></oxd-icon>
      <oxd-text v-show="widgetName">
        {{ widgetName }}
      </oxd-text>
      <oxd-icon name="setting" v-show="showSettings"></oxd-icon>
    </div>
    <oxd-divider />
  </oxd-sheet>
</template>
<script>
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Icon from '@ohrm/oxd/core/components/Icon/Icon';

export default {
  name: 'WidgetCard',
  components: {
    'oxd-sheet': Sheet,
    'oxd-icon': Icon,
  },
  props: {
    iconName: {
      type: String,
      required: true,
    },
    widgetName: {
      type: String,
      default: '',
    },
    showSettings: {
      type: Boolean,
      default: false,
    },
    noContentText: {
      type: String,
    }
  }
};
</script>
<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-dashboard-widget{
  height: 4rem;
  overflow: hidden;
  padding: 0.5rem;
}
</style>
