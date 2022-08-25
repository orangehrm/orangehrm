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
          :name="icon"
        ></oxd-icon>
        <oxd-text v-show="title" type="card-title">
          {{ title }}
        </oxd-text>
      </div>
      <slot v-if="hasActionSlot" name="action"></slot>
    </div>
    <oxd-divider />
    <oxd-loading-spinner
      v-if="loading"
      class="orangehrm-dashboard-widget-loader"
    />
    <div v-else class="orangehrm-dashboard-widget-body">
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
  </oxd-sheet>
</template>
<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner';

export default {
  name: 'BaseWidget',
  components: {
    'oxd-sheet': Sheet,
    'oxd-icon': Icon,
    'oxd-loading-spinner': Spinner,
  },
  props: {
    icon: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: '',
    },
    emptyContentText: {
      type: String,
      required: true,
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
<style src="./base-widget.scss" lang="scss" scoped></style>
