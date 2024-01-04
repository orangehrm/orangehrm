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
    <div
      v-if="post.photoIds.length > 1"
      class="orangehrm-photo-viewer-controls"
    >
      <oxd-icon-button
        class="orangehrm-photo-viewer-icon actions"
        name="chevron-left"
        :disabled="index === 0"
        @click="onClickPreviousPhoto"
      />
      <oxd-icon-button
        class="orangehrm-photo-viewer-icon actions"
        name="chevron-right"
        :disabled="index === post.photoIds.length - 1"
        @click="onClickNextPhoto"
      />
    </div>
    <div class="orangehrm-photo-viewer-actions">
      <slot></slot>
    </div>

    <oxd-icon-button
      class="orangehrm-photo-viewer-close actions"
      name="x"
      @click="onClickClose"
    />
  </div>
</template>

<script>
import {computed, onBeforeUnmount, reactive, toRefs} from 'vue';

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
      const photo = props.post.photoIds[state.index];
      return `${window.appGlobal.baseUrl}/buzz/photo/${photo}`;
    });

    const onClickClose = ($event) => {
      if ($event.key && $event.key !== 'Escape') return;
      context.emit('close');
    };

    window.addEventListener('keydown', onClickClose);

    onBeforeUnmount(() => window.removeEventListener('keydown', onClickClose));

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
