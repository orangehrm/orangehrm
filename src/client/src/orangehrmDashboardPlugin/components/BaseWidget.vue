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
  <oxd-sheet :gutters="false" class="orangehrm-dashboard-widget">
    <div class="orangehrm-dashboard-widget-header">
      <div class="orangehrm-dashboard-widget-name">
        <oxd-icon
          :name="icon"
          :type="iconType"
          class="orangehrm-dashboard-widget-icon"
        ></oxd-icon>
        <oxd-text tag="p">
          {{ title }}
        </oxd-text>
      </div>
      <slot name="action"></slot>
    </div>
    <oxd-divider />
    <oxd-loading-spinner
      v-if="loading"
      class="orangehrm-dashboard-widget-loader"
    />
    <div v-else ref="widgetRef" :class="widgetBodyClasses">
      <slot></slot>
      <div v-if="empty" class="orangehrm-dashboard-widget-body-nocontent">
        <img
          :src="defaultPic"
          alt="No Content"
          class="orangehrm-dashboard-widget-img"
        />
        <oxd-text tag="p">
          {{ emptyText || $t('dashboard.not_available') }}
        </oxd-text>
      </div>
    </div>
  </oxd-sheet>
</template>

<script>
import {computed, ref} from 'vue';
import {OxdIcon, OxdSheet, OxdSpinner} from '@ohrm/oxd';

export default {
  name: 'BaseWidget',
  components: {
    'oxd-sheet': OxdSheet,
    'oxd-icon': OxdIcon,
    'oxd-loading-spinner': OxdSpinner,
  },
  props: {
    icon: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    empty: {
      type: Boolean,
      default: false,
    },
    emptyText: {
      type: String,
      default: null,
    },
    iconType: {
      type: String,
      default: undefined,
    },
  },
  setup() {
    const widgetRef = ref();
    const defaultPic = `${window.appGlobal.publicPath}/images/dashboard_empty_widget_watermark.png`;

    const widgetBodyClasses = computed(() => ({
      'orangehrm-dashboard-widget-body': true,
      '--scroll-visible':
        widgetRef.value?.scrollHeight > widgetRef.value?.clientHeight,
    }));

    return {
      widgetRef,
      defaultPic,
      widgetBodyClasses,
    };
  },
};
</script>

<style src="./base-widget.scss" lang="scss" scoped></style>
