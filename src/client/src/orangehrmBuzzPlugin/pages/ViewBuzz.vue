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
  <oxd-tab-container v-if="isMobile" v-model="tabSelector">
    <oxd-tab-panel key="buzz_newsfeed" :name="$t('buzz.buzz_newsfeed')">
      <news-feed :mobile="true" :employee="employee"></news-feed>
    </oxd-tab-panel>
    <oxd-tab-panel
      key="buzz_anniversary"
      :name="$t('buzz.upcoming_anniversaries')"
    >
      <upcoming-anniversaries></upcoming-anniversaries>
    </oxd-tab-panel>
  </oxd-tab-container>
  <oxd-grid v-else :cols="2" class="orangehrm-buzz-layout">
    <oxd-grid-item>
      <news-feed :employee="employee"></news-feed>
    </oxd-grid-item>
    <oxd-grid-item>
      <upcoming-anniversaries></upcoming-anniversaries>
    </oxd-grid-item>
  </oxd-grid>
</template>

<script>
import {computed, ref} from 'vue';
import useResponsive, {
  DEVICE_LG,
  DEVICE_XL,
} from '@ohrm/oxd/composables/useResponsive';
import TabPanel from '@ohrm/oxd/core/components/Tab/TabPanel';
import NewsFeed from '@/orangehrmBuzzPlugin/components/NewsFeed.vue';
import TabContainer from '@ohrm/oxd/core/components/Tab/TabContainer';
import UpcomingAnniversaries from '@/orangehrmBuzzPlugin/components/UpcomingAnniversaries.vue';

export default {
  components: {
    'news-feed': NewsFeed,
    'oxd-tab-panel': TabPanel,
    'oxd-tab-container': TabContainer,
    'upcoming-anniversaries': UpcomingAnniversaries,
  },

  props: {
    employee: {
      type: Object,
      required: true,
    },
  },

  setup() {
    const tabSelector = ref(null);
    const responsiveState = useResponsive();

    const isMobile = computed(() => {
      return !(
        responsiveState.screenType === DEVICE_LG ||
        responsiveState.screenType === DEVICE_XL
      );
    });

    return {
      isMobile,
      tabSelector,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-layout {
  justify-content: center;
  grid-template-columns: minmax(240px, 640px) minmax(0, 375px);
}
::v-deep(.oxd-tab-bar) {
  width: 100%;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
</style>
