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
  <div :class="classes">
    <div
      v-for="(photo, index) in photos"
      :key="photo"
      class="orangehrm-buzz-photos-item"
    >
      <slot name="content" :data="photo" :index="index"></slot>
      <img :src="photo" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'PhotoFrame',
  props: {
    media: {
      type: Array,
      required: true,
    },
  },
  computed: {
    classes() {
      return {
        'orangehrm-buzz-photos': true,
        '--two-thumbnails': this.media.length === 2,
        '--three-thumbnails': this.media.length === 3,
        '--four-thumbnails': this.media.length === 4,
        '--five-thumbnails': this.media.length === 5,
      };
    },
    photos() {
      return (this.media || []).map((photo) => {
        if (typeof photo === 'number') {
          return `${window.appGlobal.baseUrl}/buzz/photo/${photo}`;
        }
        const {type, base64} = photo;
        return `data:${type};base64,${base64}`;
      });
    },
  },
};
</script>

<style src="./photo-frame.scss" lang="scss" scoped></style>
