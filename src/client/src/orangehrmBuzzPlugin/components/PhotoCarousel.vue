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
  <div v-if="!mobile" class="orangehrm-photo-carousel web">
    <photo-viewer
      :post="post"
      :photo-index="photoIndex"
      @close="$emit('close', false)"
    ></photo-viewer>
    <post-details :post="post"></post-details>
  </div>
  <div v-else class="orangehrm-photo-carousel">
    <photo-viewer
      :post="post"
      :photo-index="photoIndex"
      @close="$emit('close', false)"
    ></photo-viewer>
  </div>
</template>

<script>
import {computed, reactive, toRefs} from 'vue';
import PhotoViewer from '@/orangehrmBuzzPlugin/components/PhotoViewer';
import PostDetails from '@/orangehrmBuzzPlugin/components/PostDetails';

export default {
  name: 'PhotoCarousel',

  components: {
    'photo-viewer': PhotoViewer,
    'post-details': PostDetails,
  },

  props: {
    post: {
      type: Object,
      required: true,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
    photoIndex: {
      type: Number,
      required: true,
    },
  },

  emits: ['close'],

  setup(props) {
    const state = reactive({
      index: props.photoIndex,
    });

    const selectedPhoto = computed(() => props.post.photo[state.index]);

    const onClickNextPhoto = () => state.index++;

    const onClickPreviousPhoto = () => state.index--;

    return {
      selectedPhoto,
      onClickNextPhoto,
      onClickPreviousPhoto,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./photo-carousel.scss" lang="scss" scoped></style>
