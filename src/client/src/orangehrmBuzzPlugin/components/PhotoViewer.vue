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
  <div class="orangehrm-photo-viewer">
    <img
      class="orangehrm-photo-viewer-background"
      alt="photo"
      :src="selectedPhoto"
    />
    <img
      class="orangehrm-photo-viewer-photo"
      alt="background"
      :src="selectedPhoto"
    />
    <div v-if="post.photo.length > 1" class="orangehrm-photo-viewer-controls">
      <oxd-icon-button
        class="orangehrm-photo-viewer-icon"
        name="chevron-left"
        :disabled="index === 0"
        @click="onClickPreviousPhoto"
      />
      <oxd-icon-button
        class="orangehrm-photo-viewer-icon"
        name="chevron-right"
        :disabled="index === post.photo.length - 1"
        @click="onClickNextPhoto"
      />
    </div>
    <oxd-icon-button
      class="orangehrm-photo-viewer-close"
      name="x"
      @click="onClickClose"
    />
  </div>
</template>

<script>
import {computed, reactive, toRefs} from 'vue';

export default {
  name: 'PhotoViewer',

  props: {
    post: {
      type: Object,
      required: true,
    },
    photoIndex: {
      type: Number,
      required: true,
    },
  },

  emits: ['close'],

  setup(props, context) {
    const state = reactive({
      index: props.photoIndex,
    });

    const onClickNextPhoto = () => state.index++;

    const onClickPreviousPhoto = () => state.index--;

    const selectedPhoto = computed(() => {
      const {type, base64} = props.post.photo[state.index];
      return `data:${type};base64,${base64}`;
    });

    const onClickClose = () => context.emit('close');

    return {
      onClickClose,
      selectedPhoto,
      onClickNextPhoto,
      onClickPreviousPhoto,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./photo-viewer.scss" lang="scss" scoped></style>
