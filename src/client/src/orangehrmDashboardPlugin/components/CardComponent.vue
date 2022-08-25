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
    <div class="orangehrm-dashboard-widget-header">
      <div class="orangehrm-dashboard-widget-name">
        <oxd-icon
          class="orangehrm-dashboard-widget-icon"
          :name="iconName"
        ></oxd-icon>
        <slot name="widget-settings"></slot>
        <oxd-text v-show="widgetName" type="card-title">
          {{ widgetName }}
        </oxd-text>
      </div>
      <div>
        <slot v-if="hasActionSlot" name="action"></slot>
      </div>
    </div>
    <oxd-divider />
    <div class="orangehrm-dashboard-widget-body">
      <slot name="body"></slot>
      <div
        v-if="!hasBodySlot"
        class="orangehrm-dashboard-widget-body-nocontent"
      >
        <img
          :src="defaultPic"
          alt="No Content"
          class="orangehrm-dashboard-widget-img"
        />
        <oxd-text tag="p">
          {{ emptyContentText }}
        </oxd-text>
      </div>
    </div>
    <oxd-loading-spinner v-if="isLoading" class="orangehrm-container-loader" />
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
    emptyContentText: {
      type: String,
      default: '',
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  setup() {
    const defaultPic = `${window.appGlobal.baseUrl}/../images/dashboard_empty_widget_watermark.png`;

    return {
      defaultPic,
    };
  },
  computed: {
    hasBodySlot() {
      return !!this.$slots.body;
    },
    hasActionSlot() {
      return !!this.$slots.action;
    },
  },
};
</script>
<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';
.orangehrm-dashboard-widget {
  height: 390px;
  max-width: 348px;
  padding: 0.75rem;
  margin-bottom: 1.5rem;

  &-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  &-name {
    display: flex;
    align-items: center;
  }

  &-icon {
    margin-right: 0.5rem;
  }

  &-body {
    &-nocontent {
      text-align: center;
      font-size: 10px;
      margin-top: 2.5rem;
    }
  }

  &-watermark {
    width: 175px;
    margin: 60px auto auto;
    text-align: center;
  }

  &-img {
    width: 60%;
  }
}
</style>
