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
  <post-modal :title="$t('buzz.share_video')" @submit="onSubmit">
    <template #header>
      <oxd-buzz-post-input
        v-model="post.text"
        :rules="rules.text"
        :placeholder="$t('buzz.post_placeholder')"
      >
      </oxd-buzz-post-input>
    </template>
    <oxd-input-field
      v-model="post.url"
      type="textarea"
      :rules="rules.url"
      :label="$t('buzz.video_url')"
      :placeholder="$t('buzz.paste_video_url')"
    />
    <video-frame v-if="isValidURL" :video-src="post.url"></video-frame>
  </post-modal>
</template>

<script>
import {computed, reactive, toRefs} from 'vue';
import {
  required,
  validVideoURL,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import PostModal from '@/orangehrmBuzzPlugin/components/PostModal.vue';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame.vue';
import BuzzPostInput from '@ohrm/oxd/core/components/Buzz/BuzzPostInput';

export default {
  name: 'ShareVideoModal',

  components: {
    'post-modal': PostModal,
    'video-frame': VideoFrame,
    'oxd-buzz-post-input': BuzzPostInput,
  },

  setup(props) {
    const rules = {
      text: [required, shouldNotExceedCharLength(63535)],
      url: [required, validVideoURL],
    };

    const state = reactive({
      post: {
        text: null,
        url: null,
      },
    });

    const onSubmit = () => {
      // do something
    };

    const isValidURL = computed(
      () => !!state.post.url && validVideoURL(state.post.url) === true,
    );

    return {
      rules,
      onSubmit,
      isValidURL,
      ...toRefs(state),
    };
  },
};
</script>
