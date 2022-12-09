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
  <post-modal
    :loading="isLoading"
    :title="$t('buzz.edit_post')"
    :action-label="$t('buzz.post')"
    @submit="onSubmit"
    @close="$emit('close', false)"
  >
    <template #header>
      <oxd-buzz-post-input v-model="post.text" :rules="rules.text">
      </oxd-buzz-post-input>
    </template>
    <photo-input
      v-if="post.type === 'text' || post.type === 'photo'"
      v-model="post.photos"
    />
    <oxd-input-field
      v-if="post.type === 'video'"
      v-model="post.video"
      type="textarea"
      :rules="rules.url"
      :label="$t('buzz.video_url')"
    />
    <video-frame v-if="embedURL" :video-src="embedURL"> </video-frame>
  </post-modal>
</template>

<script>
import {reactive, toRefs} from 'vue';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import usei18n from '@/core/util/composable/usei18n';
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import promiseDebounce from '@ohrm/oxd/utils/promiseDebounce';
import PostModal from '@/orangehrmBuzzPlugin/components/PostModal';
import VideoFrame from '@/orangehrmBuzzPlugin/components/VideoFrame';
import PhotoInput from '@/orangehrmBuzzPlugin/components/PhotoInput';
import BuzzPostInput from '@ohrm/oxd/core/components/Buzz/BuzzPostInput';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';

export default {
  name: 'EditPostModal',

  components: {
    'post-modal': PostModal,
    'photo-input': PhotoInput,
    'video-frame': VideoFrame,
    'oxd-buzz-post-input': BuzzPostInput,
  },

  props: {
    data: {
      type: Object,
      required: true,
    },
  },

  emits: ['close'],

  setup(props, context) {
    const {$t} = usei18n();
    const http = new APIService(window.appGlobal.baseUrl, '');
    const {updateSuccess} = useToast();
    const {updatePost} = useBuzzAPIs(http);

    const state = reactive({
      post: {
        text: props.data.text,
        type: props.data.type,
        photos: props.data.photoIds || [],
        video: props.data.video?.link || null,
      },
      isLoading: false,
      embedURL: props.data.video?.link || null,
    });

    const onSubmit = () => {
      let type = 'text';
      state.isLoading = true;

      if (state.post.photos.length > 0) {
        type = 'photo';
      }
      if (state.post.video) {
        type = 'video';
      }

      updatePost(props.data.id, {
        type: type,
        text: state.post.text,
        link: state.post.video,
        photos: state.post.photos.filter(id => typeof id === 'object'),
        deletedPhotos: (props.data.photoIds || []).filter(id => {
          return state.post.photos.findIndex(photo => photo === id) === -1;
        }),
      }).then(response => {
        updateSuccess();
        context.emit('close', response.data);
      });
    };

    const rules = {
      url: [
        required,
        promiseDebounce(async value => {
          if (!value) return true;
          state.embedURL = null;
          const response = await http.request({
            method: 'GET',
            url: 'api/v2/buzz/validation/links',
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
      text: [
        shouldNotExceedCharLength(65530),
        value => {
          if (props.data.type === 'video' || state.post.photos.length > 0) {
            return true;
          }
          return required(value);
        },
      ],
    };

    return {
      rules,
      onSubmit,
      ...toRefs(state),
    };
  },
};
</script>
