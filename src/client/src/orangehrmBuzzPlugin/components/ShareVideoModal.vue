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
  <post-modal
    :loading="isLoading"
    :title="$t('buzz.share_video')"
    @submit="onSubmit"
    @close="$emit('close', false)"
  >
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
    <video-frame v-if="embedURL" :video-src="embedURL"></video-frame>
  </post-modal>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {reactive, toRefs} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import {APIService} from '@/core/util/services/api.service';
import PostModal from '@/orangehrmBuzzPlugin/components/PostModal.vue';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame.vue';
import {OxdBuzzPostInput, promiseDebounce} from '@ohrm/oxd';

export default {
  name: 'ShareVideoModal',

  components: {
    'post-modal': PostModal,
    'video-frame': VideoFrame,
    'oxd-buzz-post-input': OxdBuzzPostInput,
  },

  props: {
    text: {
      type: String,
      default: null,
    },
  },

  emits: ['close'],

  setup(props, context) {
    const {$t} = usei18n();
    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/buzz/posts');

    const state = reactive({
      post: {
        text: props.text || null,
        url: null,
      },
      embedURL: null,
      isLoading: false,
    });

    const rules = {
      url: [
        required,
        promiseDebounce(async (value) => {
          if (!value) return true;
          state.embedURL = null;
          const response = await http.request({
            method: 'GET',
            url: '/api/v2/buzz/validation/links',
            params: {
              url: value,
            },
          });
          const {data} = response.data;
          if (data?.valid === true) {
            state.embedURL = data.embeddedURL;
            return true;
          } else {
            return $t('general.invalid_video_url_message');
          }
        }, 500),
      ],
      text: [shouldNotExceedCharLength(65530)],
    };

    const onSubmit = () => {
      state.isLoading = true;
      http
        .create({
          type: 'video',
          link: state.post.url,
          text: state.post.text,
        })
        .then(() => context.emit('close', true));
    };

    return {
      rules,
      onSubmit,
      ...toRefs(state),
    };
  },
};
</script>
